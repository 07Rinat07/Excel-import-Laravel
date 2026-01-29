<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Export\CustomExportRequest;
use App\Models\ExportLog;
use App\Models\ExcelTemplate;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use App\Services\Export\ProjectExportService;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function index(): Response
    {
        $templatesByType = ExcelTemplate::query()
            ->with('columns')
            ->where('is_active', true)
            ->orderByDesc('id')
            ->get()
            ->groupBy('type_id');

        $types = Type::query()
            ->orderBy('title')
            ->get()
            ->map(function (Type $type) use ($templatesByType) {
                $template = $templatesByType->get($type->id)?->first();

                return [
                    'id' => $type->id,
                    'title' => $type->title,
                    'template' => $template ? [
                        'id' => $template->id,
                        'columns' => $template->columns->map(function ($column) {
                            return [
                                'id' => $column->id,
                                'key' => $column->key,
                                'label' => $column->label,
                                'data_type' => $column->data_type,
                                'is_required' => $column->is_required,
                            ];
                        })->all(),
                    ] : null,
                ];
            })
            ->values();

        $tasks = Task::query()
            ->with(['file', 'template.columns', 'typeModel'])
            ->latest('id')
            ->limit(50)
            ->get()
            ->map(function (Task $task) {
                $template = $task->template;
                if (! $template && $task->type_id) {
                    $template = ExcelTemplate::query()
                        ->with('columns')
                        ->where('type_id', $task->type_id)
                        ->where('is_active', true)
                        ->latest('id')
                        ->first();
                }

                return [
                    'id' => $task->id,
                    'title' => $task->file?->title ?? 'Task '.$task->id,
                    'type' => $task->typeModel ? [
                        'id' => $task->typeModel->id,
                        'title' => $task->typeModel->title,
                    ] : null,
                    'template' => $template ? [
                        'id' => $template->id,
                        'columns' => $template->columns->map(function ($column) {
                            return [
                                'id' => $column->id,
                                'key' => $column->key,
                                'label' => $column->label,
                                'data_type' => $column->data_type,
                                'is_required' => $column->is_required,
                            ];
                        })->all(),
                    ] : null,
                ];
            })
            ->values();

        $exports = ExportLog::query()
            ->with('user')
            ->latest('id')
            ->paginate(15)
            ->through(function (ExportLog $log) {
                return [
                    'id' => $log->id,
                    'source_type' => $log->source_type,
                    'source_id' => $log->source_id,
                    'format' => $log->format,
                    'status' => $log->status,
                    'file_name' => $log->file_name,
                    'user' => $log->user ? [
                        'id' => $log->user->id,
                        'name' => $log->user->name,
                        'email' => $log->user->email,
                    ] : null,
                    'created_at' => $log->created_at?->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Admin/Exports/Index', [
            'exports' => $exports,
            'types' => $types,
            'tasks' => $tasks,
            'users' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email'])
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ])->all(),
        ]);
    }

    public function download(CustomExportRequest $request, ProjectExportService $service): BinaryFileResponse
    {
        $data = $request->validated();
        $sourceType = $data['source_type'];
        $sourceId = (int) $data['source_id'];
        $format = $data['format'];
        $columnIds = array_values(array_unique($data['columns']));
        $labels = $data['labels'] ?? [];
        $userId = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $sheetName = $data['sheet_name'] ?? null;
        $sheetIndex = isset($data['sheet_index']) ? (int) $data['sheet_index'] : null;

        if ($sourceType === 'type') {
            $type = Type::findOrFail($sourceId);
            $this->authorize('export', $type);
            $template = ExcelTemplate::query()
                ->with('columns')
                ->where('type_id', $type->id)
                ->where('is_active', true)
                ->latest('id')
                ->firstOrFail();
            $columnIds = $this->filterColumns($columnIds, $template);
            $labels = $this->filterLabels($labels, $columnIds);
            $filename = "type-{$type->id}-custom.{$format}";
            try {
                $response = $service->exportCustomByType($type, $format, $columnIds, $labels, $request->user(), $userId, $sheetName, $sheetIndex);
                $this->logExport($request->user(), 'type', $type->id, $format, 'success', $filename);

                return $response;
            } catch (\Throwable $e) {
                $this->logExport($request->user(), 'type', $type->id, $format, 'failed', $filename);
                throw $e;
            }
        }

        $task = Task::with('template.columns')->findOrFail($sourceId);
        $this->authorize('view', $task);
        $template = $task->template;
        if (! $template) {
            abort(422, 'Template is missing for this task.');
        }
        $columnIds = $this->filterColumns($columnIds, $template);
        $labels = $this->filterLabels($labels, $columnIds);
        $filename = "task-{$task->id}-custom.{$format}";
        try {
        $response = $service->exportCustomByTask($task, $format, $columnIds, $labels, $sheetName, $sheetIndex);
        $this->logExport($request->user(), 'task', $task->id, $format, 'success', $filename);

            return $response;
        } catch (\Throwable $e) {
            $this->logExport($request->user(), 'task', $task->id, $format, 'failed', $filename);
            throw $e;
        }
    }

    private function filterColumns(array $columnIds, ExcelTemplate $template): array
    {
        $allowed = $template->columns()->pluck('id')->all();
        $filtered = array_values(array_intersect($columnIds, $allowed));
        if (! $filtered) {
            abort(422, 'No valid columns selected for export.');
        }

        return $filtered;
    }

    private function filterLabels(array $labels, array $columnIds): array
    {
        $filtered = [];
        foreach ($columnIds as $id) {
            if (array_key_exists($id, $labels)) {
                $filtered[$id] = (string) $labels[$id];
            }
        }

        return $filtered;
    }

    private function logExport(?\App\Models\User $user, string $sourceType, int $sourceId, string $format, string $status, string $fileName): void
    {
        ExportLog::create([
            'user_id' => $user?->id,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'format' => $format,
            'status' => $status,
            'file_name' => $fileName,
        ]);
    }
}

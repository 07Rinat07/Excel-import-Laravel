<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Export\CustomExportRequest;
use App\Jobs\QueueExportJob;
use App\Models\ExportLog;
use App\Models\ExportPreset;
use App\Models\ExcelTemplate;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use App\Services\Export\ProjectExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function index(Request $request): Response
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
                $disk = config('exports.disk', 'exports');
                $downloadable = $log->status === 'success'
                    && $log->file_name
                    && Storage::disk($disk)->exists($log->file_name);

                return [
                    'id' => $log->id,
                    'source_type' => $log->source_type,
                    'source_id' => $log->source_id,
                    'format' => $log->format,
                    'status' => $log->status,
                    'file_name' => $log->file_name,
                    'download_url' => $downloadable ? route('admin.exports.download_file', $log->id) : null,
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
            'presets' => ExportPreset::query()
                ->where('user_id', $request->user()?->id)
                ->orderBy('name')
                ->get()
                ->map(fn (ExportPreset $preset) => [
                    'id' => $preset->id,
                    'name' => $preset->name,
                    'source_type' => $preset->source_type,
                    'source_id' => $preset->source_id,
                    'format' => $preset->format,
                    'columns' => $preset->columns ?? [],
                    'labels' => $preset->labels ?? [],
                    'sheet_name' => $preset->sheet_name,
                    'sheet_index' => $preset->sheet_index,
                    'filter_user_id' => $preset->filter_user_id,
                ])
                ->values(),
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

    public function queue(CustomExportRequest $request): RedirectResponse
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
        } else {
            $task = Task::with('template.columns')->findOrFail($sourceId);
            $this->authorize('view', $task);
            $template = $task->template;
            if (! $template) {
                return redirect()->back()->withErrors(['columns' => 'Template is missing for this task.']);
            }
            $columnIds = $this->filterColumns($columnIds, $template);
            $labels = $this->filterLabels($labels, $columnIds);
        }

        $log = ExportLog::create([
            'user_id' => $request->user()?->id,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'format' => $format,
            'status' => 'pending',
            'file_name' => null,
            'columns_count' => count($columnIds),
        ]);

        QueueExportJob::dispatch(
            $log->id,
            $sourceType,
            $sourceId,
            $format,
            $columnIds,
            $labels,
            $userId,
            $sheetName,
            $sheetIndex
        )->onQueue('exports');

        return redirect()->back()->with('message', 'Export queued.');
    }

    public function storePreset(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'source_type' => 'required|string|in:type,task',
            'source_id' => 'required|integer',
            'format' => 'required|string|in:xlsx,csv,tsv',
            'user_id' => 'nullable|integer|exists:users,id',
            'sheet_name' => 'nullable|string|max:120',
            'sheet_index' => 'nullable|integer|min:0',
            'columns' => 'required|array|min:1',
            'columns.*' => 'integer',
            'labels' => 'nullable|array',
            'labels.*' => 'nullable|string|max:120',
        ]);

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
        } else {
            $task = Task::with('template.columns')->findOrFail($sourceId);
            $this->authorize('view', $task);
            $template = $task->template;
            if (! $template) {
                return redirect()->back()->withErrors(['columns' => 'Template is missing for this task.']);
            }
            $columnIds = $this->filterColumns($columnIds, $template);
            $labels = $this->filterLabels($labels, $columnIds);
        }

        ExportPreset::create([
            'user_id' => $request->user()?->id,
            'name' => $data['name'],
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'format' => $format,
            'columns' => $columnIds,
            'labels' => $labels,
            'sheet_name' => $sheetName,
            'sheet_index' => $sheetIndex,
            'filter_user_id' => $userId,
        ]);

        return redirect()->back()->with('message', 'Preset saved.');
    }

    public function destroyPreset(Request $request, ExportPreset $preset): RedirectResponse
    {
        $userId = $request->user()?->id;
        if ($preset->user_id && $preset->user_id !== $userId) {
            abort(403);
        }

        $preset->delete();

        return redirect()->back()->with('message', 'Preset deleted.');
    }

    public function downloadFile(int $id)
    {
        $log = ExportLog::findOrFail($id);
        if ($log->status !== 'success' || ! $log->file_name) {
            abort(404);
        }

        $disk = config('exports.disk', 'exports');
        if (! Storage::disk($disk)->exists($log->file_name)) {
            abort(404);
        }

        return Storage::disk($disk)->download($log->file_name, basename($log->file_name));
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

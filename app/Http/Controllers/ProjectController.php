<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ImportStoreRequest;
use App\Http\Requests\Project\ImportMapStoreRequest;
use App\Http\Requests\Project\ProjectIndexRequest;
use App\Http\Resources\Project\ProjectResource;
use App\Jobs\ImportProjectExcelFileJob;
use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Services\SheetProcessingService;
use App\Services\SmartMappingService;
use Database\Seeders\TypesSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    public function index(ProjectIndexRequest $request)
    {
        $this->authorize('viewAny', Project::class);

        $data = $request->validated();
        $typeId = isset($data['type_id']) ? (int) $data['type_id'] : null;
        $taskId = isset($data['task_id']) ? (int) $data['task_id'] : null;
        $templateId = isset($data['template_id']) ? (int) $data['template_id'] : null;
        $search = trim((string) ($data['q'] ?? ''));
        $columnFilters = $data['filters'] ?? [];

        $query = Project::query()
            ->visibleTo($request->user())
            ->with(['type', 'values.column'])
            ->when($typeId, fn ($builder) => $builder->where('type_id', $typeId))
            ->when($taskId, fn ($builder) => $builder->where('task_id', $taskId))
            ->when($templateId, fn ($builder) => $builder->where('template_id', $templateId))
            ->when($search !== '', function ($builder) use ($search) {
                $builder->whereHas('values', function ($valueQuery) use ($search) {
                    $valueQuery->where('value', 'like', '%'.$search.'%');
                });
            });

        if (is_array($columnFilters)) {
            foreach ($columnFilters as $key => $value) {
                $value = trim((string) $value);
                if ($value === '') {
                    continue;
                }
                $query->whereHas('values', function ($valueQuery) use ($key, $value) {
                    $valueQuery->where('value', 'like', '%'.$value.'%')
                        ->whereHas('column', function ($columnQuery) use ($key) {
                            $columnQuery->where('key', $key);
                        });
                });
            }
        }

        $projects = $query->latest('id')->paginate(10)->withQueryString();
        $projectsResource = ProjectResource::collection($projects);

        $columns = $this->resolveColumns($templateId, $typeId, $projects->getCollection());

        $types = Type::query()->orderBy('title')->get(['id', 'title']);
        $tasks = Task::query()
            ->visibleTo($request->user())
            ->with('file')
            ->latest('id')
            ->limit(50)
            ->get()
            ->map(function (Task $task) {
                return [
                    'id' => $task->id,
                    'label' => $task->file?->title ?? 'Task '.$task->id,
                ];
            });

        $templates = ExcelTemplate::query()
            ->orderBy('name')
            ->get(['id', 'name', 'type_id']);

        return inertia('Project/Index', [
            'projects' => $projectsResource,
            'types' => $types,
            'tasks' => $tasks,
            'templates' => $templates,
            'columns' => $columns,
            'filters' => [
                'type_id' => $typeId,
                'task_id' => $taskId,
                'template_id' => $templateId,
                'q' => $search,
                'columns' => is_array($columnFilters) ? $columnFilters : [],
            ],
        ]);
    }

    private function resolveColumns(?int $templateId, ?int $typeId, $projects): array
    {
        $template = null;

        if ($templateId) {
            $template = ExcelTemplate::with('columns')->find($templateId);
        } elseif ($typeId) {
            $template = ExcelTemplate::with('columns')
                ->where('type_id', $typeId)
                ->where('is_active', true)
                ->latest('id')
                ->first();
        }

        if (! $template && $projects->count()) {
            $firstTemplateId = $projects->first()->template_id;
            if ($firstTemplateId) {
                $template = ExcelTemplate::with('columns')->find($firstTemplateId);
            }
        }

        if (! $template) {
            return [];
        }

        return $template->columns
            ->sortBy('position')
            ->values()
            ->map(function ($column) {
                return [
                    'id' => $column->id,
                    'key' => $column->key,
                    'label' => $column->label,
                ];
            })
            ->all();
    }

    public function import(Request $request)
    {
        $this->authorize('viewAny', Project::class);

        $types = Type::query()->orderBy('title')->get(['id', 'title']);
        $recentTasks = Task::query()
            ->visibleTo($request->user())
            ->with(['file', 'typeModel'])
            ->latest('id')
            ->limit(3)
            ->get()
            ->map(function (Task $task) {
                return [
                    'id' => $task->id,
                    'file_title' => $task->file?->title ?? 'File '.$task->id,
                    'type_title' => $task->typeModel?->title ?? '',
                    'status_key' => $this->taskStatusKey($task->status),
                    'created_at' => $task->created_at?->format('Y-m-d H:i') ?? null,
                    'map_url' => $task->status === Task::STATUS_PENDING ? route('project.import.map', $task) : null,
                ];
            })->all();

        return inertia('Project/ImportWizard', [
            'types' => $types,
            'recentTasks' => $recentTasks,
        ]);
    }

    public function importStore(ImportStoreRequest $request)
    {
        $this->authorize('viewAny', Project::class);

        $data = $request->validated();

        $typeId = $this->resolveTypeId($data['type_id'] ?? null);
        if (! $typeId) {
            $message = 'Types are not configured. Please create a type before importing.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()->back()->withErrors(['type_id' => $message]);
        }

        $file = File::putAndCreate($data['file']);
        $task = Task::create([
            'file_id' => $file->id,
            'user_id' => auth()->id(),
            'type' => 1,
            'type_id' => $typeId,
            'status' => Task::STATUS_PENDING,
        ]);

        // Return JSON for AJAX requests, redirect for regular requests
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'task_id' => $task->id,
                'redirect_url' => route('project.import.map', $task),
            ]);
        }

        return redirect()->route('project.import.map', $task);
    }

    public function importPrepare(ImportStoreRequest $request)
    {
        $this->authorize('viewAny', Project::class);

        $data = $request->validated();

        $typeId = $this->resolveTypeId($data['type_id'] ?? null);
        if (! $typeId) {
            return response()->json([
                'message' => 'Types are not configured. Please create a type before importing.',
            ], 422);
        }

        $file = File::putAndCreate($data['file']);
        $task = Task::create([
            'file_id' => $file->id,
            'user_id' => $request->user()->id,
            'type' => 1,
            'type_id' => $typeId,
            'status' => Task::STATUS_PENDING,
        ]);

        $sheetService = app(SheetProcessingService::class);
        $mappingService = app(SmartMappingService::class);
        $headers = [];
        $previewRows = [];
        $availableSheets = [];
        $selectedSheetIndex = null;

        $path = Storage::disk('public')->path($file->path);
        $availableSheets = $sheetService->getAvailableSheets($path);
        if ($availableSheets) {
            $selectedSheetIndex = $sheetService->findBestSheet($path);
            if (! $sheetService->validateSheetIndex($path, (int) $selectedSheetIndex)) {
                $selectedSheetIndex = 0;
            }
            $task->update([
                'available_sheets' => $availableSheets,
                'selected_sheet_index' => $selectedSheetIndex,
                'sheet' => $availableSheets[$selectedSheetIndex]['name'] ?? null,
            ]);

            $rawHeaders = $sheetService->getSheetHeaders($path, (int) $selectedSheetIndex);
            $headers = $this->normalizeHeaders($rawHeaders);
            $previewRows = $sheetService->readSheetData($path, (int) $selectedSheetIndex, true, 4);
        }

        $dataTypes = ['string', 'number', 'integer', 'date', 'boolean'];

        $template = ExcelTemplate::query()
            ->where('type_id', $typeId)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $mappingSuggestion = null;
        $templateColumns = [];
        if ($template) {
            $template->load('columns');
            $headerLabels = array_map(
                fn ($header) => $header['original_label'] !== '' ? $header['original_label'] : $header['label'],
                $headers
            );
            $mappingSuggestion = $mappingService->suggestMapping($headerLabels, $template->columns);
            $templateColumns = $template->columns->mapWithKeys(function (ExcelTemplateColumn $column) {
                return [
                    $column->id => [
                        'id' => $column->id,
                        'key' => $column->key,
                        'label' => $column->label,
                        'data_type' => $column->data_type,
                        'is_required' => (bool) $column->is_required,
                        'validation_rules' => $column->validation_rules ?? [],
                    ],
                ];
            })->all();
        }

        return response()->json([
            'task' => [
                'id' => $task->id,
            ],
            'file' => [
                'id' => $file->id,
                'title' => $file->title,
            ],
            'headers' => $headers,
            'preview_rows' => $previewRows,
            'available_sheets' => $availableSheets,
            'selected_sheet_index' => $selectedSheetIndex,
            'data_types' => $dataTypes,
            'mapping_suggestion' => $mappingSuggestion,
            'template_columns' => $templateColumns,
        ]);
    }

    private function resolveTypeId(?int $typeId): ?int
    {
        if ($typeId) {
            return Type::query()->whereKey($typeId)->exists() ? $typeId : null;
        }

        if (! Type::query()->exists()) {
            app(TypesSeeder::class)->run();
        }

        return Type::query()->value('id');
    }

    public function importMap(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $sheetService = app(SheetProcessingService::class);
        $mappingService = app(SmartMappingService::class);

        $headers = [];
        $previewRows = [];
        $availableSheets = [];
        $selectedSheetIndex = $task->selected_sheet_index ?? null;

        if ($task->file) {
            $path = Storage::disk('public')->path($task->file->path);
            $availableSheets = $sheetService->getAvailableSheets($path);
            $requestedIndex = $request->query('sheet_index');
            if ($requestedIndex !== null) {
                $selectedSheetIndex = (int) $requestedIndex;
            }

            if ($selectedSheetIndex === null) {
                $selectedSheetIndex = $sheetService->findBestSheet($path);
            }

            if (! $sheetService->validateSheetIndex($path, (int) $selectedSheetIndex)) {
                $selectedSheetIndex = 0;
            }

            if ($availableSheets) {
                $task->update([
                    'available_sheets' => $availableSheets,
                    'selected_sheet_index' => $selectedSheetIndex,
                    'sheet' => $availableSheets[$selectedSheetIndex]['name'] ?? null,
                ]);
            }

            $rawHeaders = $sheetService->getSheetHeaders($path, (int) $selectedSheetIndex);
            $headers = $this->normalizeHeaders($rawHeaders);
            $previewRows = $sheetService->readSheetData($path, (int) $selectedSheetIndex, true, 4);
        }

        $dataTypes = ['string', 'number', 'integer', 'date', 'boolean'];

        $template = ExcelTemplate::query()
            ->where('type_id', $task->type_id)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $mappingSuggestion = null;
        $templateColumns = [];
        if ($template) {
            $template->load('columns');
            $headerLabels = array_map(
                fn ($header) => $header['original_label'] !== '' ? $header['original_label'] : $header['label'],
                $headers
            );
            $mappingSuggestion = $mappingService->suggestMapping($headerLabels, $template->columns);
            $templateColumns = $template->columns->mapWithKeys(function (ExcelTemplateColumn $column) {
                return [
                    $column->id => [
                        'id' => $column->id,
                        'key' => $column->key,
                        'label' => $column->label,
                        'data_type' => $column->data_type,
                        'is_required' => (bool) $column->is_required,
                        'validation_rules' => $column->validation_rules ?? [],
                    ],
                ];
            })->all();
        }

        return inertia('Project/ImportMap', [
            'task' => [
                'id' => $task->id,
                'file' => $task->file ? [
                    'id' => $task->file->id,
                    'title' => $task->file->title,
                ] : null,
            ],
            'type' => Type::query()->find($task->type_id, ['id', 'title']),
            'headers' => $headers,
            'preview_rows' => $previewRows,
            'available_sheets' => $availableSheets,
            'selected_sheet_index' => $selectedSheetIndex,
            'data_types' => $dataTypes,
            'mapping_suggestion' => $mappingSuggestion,
            'template_columns' => $templateColumns,
        ]);
    }

    public function importMapStore(ImportMapStoreRequest $request, Task $task)
    {
        $this->authorize('view', $task);

        try {
            $this->applyMapping($task, $request->validated(), $request->user());
        } catch (ValidationException $exception) {
            return redirect()->back()->withErrors($exception->errors());
        }

        return redirect()->route('task.index')->with(['message' => 'Excel import in process']);
    }

    public function importMapStoreJson(ImportMapStoreRequest $request, Task $task)
    {
        $this->authorize('view', $task);

        $this->applyMapping($task, $request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
        ]);
    }

    private function applyMapping(Task $task, array $data, \App\Models\User $user): void
    {
        $columns = $data['columns'] ?? [];
        $columns = array_values(array_filter($columns, fn ($column) => ($column['include'] ?? false)));

        if (! $columns) {
            throw ValidationException::withMessages(['mapping' => 'Выберите хотя бы одну колонку для импорта.']);
        }

        $fileTitle = $task->file?->title ?? 'file';
        $userName = $user->name ?? 'user';
        $newTemplate = ExcelTemplate::create([
            'type_id' => $task->type_id,
            'name' => 'Mapped template '.$fileTitle.' - '.$userName.' - '.now()->format('Y-m-d H:i'),
            'header_hash' => sha1(implode('|', array_map(fn ($column) => (string) $column['name'], $columns))),
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $position = 0;
        $columnMap = [];
        $usedKeys = [];

        foreach ($columns as $column) {
            $label = trim((string) ($column['name'] ?? ''));
            if ($label === '') {
                continue;
            }

            $key = Str::slug($label, '_');
            if ($key === '') {
                $key = 'column_'.$position;
            }
            $key = $this->dedupeKey($key, $usedKeys);
            $usedKeys[] = $key;

            $newColumn = ExcelTemplateColumn::create([
                'template_id' => $newTemplate->id,
                'key' => $key,
                'label' => $label,
                'data_type' => (string) ($column['data_type'] ?? 'string'),
                'is_required' => (bool) ($column['required'] ?? false),
                'validation_rules' => $this->normalizeValidationRules($column['validation_rules'] ?? null),
                'position' => $position++,
            ]);

            $columnMap[(string) $column['index']] = $newColumn->id;
        }

        if (! $columnMap) {
            throw ValidationException::withMessages(['mapping' => 'Все выбранные колонки пустые. Укажите названия.']);
        }

        $task->update([
            'template_id' => $newTemplate->id,
            'column_map' => $columnMap,
            'status' => Task::STATUS_PROCESS,
            'total_rows' => 0,
            'imported_rows' => 0,
            'selected_sheet_index' => $data['sheet_index'] ?? $task->selected_sheet_index,
        ]);

        ImportProjectExcelFileJob::dispatch($task->file?->path ?? '', $task)->onQueue('imports');
    }

    private function normalizeHeaders(array $row): array
    {
        $headers = [];
        foreach ($row as $index => $label) {
            $normalized = trim((string) $label);
            $isUnnamed = $normalized === '';
            $headers[] = [
                'index' => $index,
                'label' => $isUnnamed ? 'Column '.($index + 1) : $normalized,
                'original_label' => $normalized,
                'is_unnamed' => $isUnnamed,
            ];
        }

        return $headers;
    }

    private function dedupeKey(string $key, array $usedKeys): string
    {
        if (! in_array($key, $usedKeys, true)) {
            return $key;
        }

        $suffix = 2;
        $candidate = $key.'_'.$suffix;
        while (in_array($candidate, $usedKeys, true)) {
            $suffix++;
            $candidate = $key.'_'.$suffix;
        }

        return $candidate;
    }

    private function taskStatusKey(int $status): string
    {
        return match ($status) {
            Task::STATUS_PENDING => 'pending',
            Task::STATUS_PROCESS => 'processing',
            Task::STATUS_SUCCESS => 'success',
            Task::STATUS_ERROR => 'error',
            default => 'unknown',
        };
    }

    private function normalizeValidationRules(mixed $rules): ?array
    {
        if (is_array($rules)) {
            $rules = array_values(array_filter(array_map('trim', $rules)));
            return $rules ?: null;
        }

        if (! is_string($rules)) {
            return null;
        }

        $rules = trim($rules);
        if ($rules === '') {
            return null;
        }

        if (str_starts_with($rules, '[')) {
            $decoded = json_decode($rules, true);
            if (is_array($decoded)) {
                $decoded = array_values(array_filter(array_map('trim', $decoded)));
                return $decoded ?: null;
            }
        }

        if (str_contains($rules, "\n")) {
            $parts = preg_split("/\r\n|\n|\r/", $rules);
        } else {
            $parts = explode('|', $rules);
        }

        $parts = array_values(array_filter(array_map('trim', $parts)));

        return $parts ?: null;
    }
}

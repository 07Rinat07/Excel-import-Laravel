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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

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

    public function import()
    {
        $this->authorize('viewAny', Project::class);

        $types = Type::query()->orderBy('title')->get(['id', 'title']);

        return inertia('Project/Import', compact('types'));
    }

    public function importStore(ImportStoreRequest $request)
    {
        $this->authorize('viewAny', Project::class);

        $data = $request->validated();

        $file = File::putAndCreate($data['file']);
        $task = Task::create([
            'file_id' => $file->id,
            'user_id' => auth()->id(),
            'type' => 1,
            'type_id' => $data['type_id'],
            'status' => Task::STATUS_PENDING,
        ]);

        return redirect()->route('project.import.map', $task);
    }

    public function importMap(Task $task)
    {
        $this->authorize('view', $task);

        $headers = $this->extractHeaders($task->file);
        $dataTypes = ['string', 'number', 'integer', 'date', 'boolean'];

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
            'data_types' => $dataTypes,
        ]);
    }

    public function importMapStore(ImportMapStoreRequest $request, Task $task)
    {
        $this->authorize('view', $task);

        $data = $request->validated();
        $columns = $data['columns'] ?? [];
        $columns = array_values(array_filter($columns, fn ($column) => ($column['include'] ?? false)));

        if (! $columns) {
            return redirect()->back()->withErrors(['mapping' => 'Выберите хотя бы одну колонку для импорта.']);
        }

        $fileTitle = $task->file?->title ?? 'file';
        $userName = $request->user()->name ?? 'user';
        $newTemplate = ExcelTemplate::create([
            'type_id' => $task->type_id,
            'name' => 'Mapped template '.$fileTitle.' - '.$userName.' - '.now()->format('Y-m-d H:i'),
            'header_hash' => sha1(implode('|', array_map(fn ($column) => (string) $column['name'], $columns))),
            'is_active' => true,
            'created_by' => $request->user()->id,
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
                'position' => $position++,
            ]);

            $columnMap[(string) $column['index']] = $newColumn->id;
        }

        if (! $columnMap) {
            return redirect()->back()->withErrors(['mapping' => 'Все выбранные колонки пустые. Укажите названия.']);
        }

        $task->update([
            'template_id' => $newTemplate->id,
            'column_map' => $columnMap,
            'status' => Task::STATUS_PROCESS,
            'total_rows' => 0,
            'imported_rows' => 0,
        ]);

        ImportProjectExcelFileJob::dispatch($task->file?->path ?? '', $task)->onQueue('imports');

        return redirect()->route('task.index')->with(['message' => 'Excel import in process']);
    }

    private function extractHeaders(?File $file): array
    {
        if (! $file) {
            return [];
        }

        $path = Storage::disk('public')->path($file->path);
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new class implements IReadFilter {
            public function readCell($column, $row, $worksheetName = ''): bool
            {
                return (int) $row === 1;
            }
        });
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();
        $row = $sheet->rangeToArray("A1:{$highestColumn}1", null, true, false)[0] ?? [];
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $headers = [];
        foreach ($row as $index => $label) {
            $label = trim((string) $label);
            if ($label === '') {
                $label = 'Column '.($index + 1);
            }
            $headers[] = [
                'index' => $index,
                'label' => $label,
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
}

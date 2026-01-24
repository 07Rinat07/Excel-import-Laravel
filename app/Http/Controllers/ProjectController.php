<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ImportStoreRequest;
use App\Http\Requests\Project\ProjectIndexRequest;
use App\Http\Resources\Project\ProjectResource;
use App\Jobs\ImportProjectExcelFileJob;
use App\Models\ExcelTemplate;
use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;

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
            'status' => Task::STATUS_PROCESS,
        ]);

        ImportProjectExcelFileJob::dispatch($file->path, $task)->onQueue('imports');

        return redirect()->back()->with(['message' => 'Excel import in process']);
    }
}

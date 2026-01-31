<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Services\Export\ProjectExportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExportSelectionController extends Controller
{
    /**
     * Show export selection UI for project
     */
    public function projectSelection(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $template = $project->template;
        $columns = $template?->columns()->orderBy('position')->get() ?? [];

        return Inertia::render('Export/ProjectSelection', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            'columns' => $columns->map(fn ($col) => [
                'id' => $col->id,
                'key' => $col->key,
                'label' => $col->label,
                'include' => true,
            ])->values()->all(),
        ]);
    }

    /**
     * Store export selection and download file for project
     */
    public function projectExport(Request $request, Project $project, ProjectExportService $service)
    {
        $this->authorize('view', $project);

        $validated = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'required|integer',
            'format' => 'required|in:xlsx,csv,tsv',
        ]);

        $template = $project->template;
        if (!$template) {
            return redirect()->back()->withErrors(['error' => 'Template not found']);
        }

        $selectedColumnIds = $this->filterColumns($validated['columns'], $template);

        return $service->exportCustomByProject($project, $validated['format'], $selectedColumnIds);
    }

    /**
     * Show export selection UI for task
     */
    public function taskSelection(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $template = $task->template;
        $columns = $template?->columns()->orderBy('position')->get() ?? [];

        return Inertia::render('Export/TaskSelection', [
            'task' => [
                'id' => $task->id,
                'status' => $task->status,
            ],
            'columns' => $columns->map(fn ($col) => [
                'id' => $col->id,
                'key' => $col->key,
                'label' => $col->label,
                'include' => true,
            ])->values()->all(),
        ]);
    }

    /**
     * Store export selection and download file for task
     */
    public function taskExport(Request $request, Task $task, ProjectExportService $service)
    {
        $this->authorize('view', $task);

        $validated = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'required|integer',
            'format' => 'required|in:xlsx,csv,tsv',
        ]);

        $template = $task->template;
        if (!$template) {
            return redirect()->back()->withErrors(['error' => 'Template not found']);
        }

        $selectedColumnIds = $this->filterColumns($validated['columns'], $template);

        return $service->exportCustomByTask($task, $validated['format'], $selectedColumnIds);
    }

    /**
     * Show export selection UI for type
     */
    public function typeSelection(Request $request, Type $type)
    {
        $this->authorize('export', $type);

        $template = $type->template;
        $columns = $template?->columns()->orderBy('position')->get() ?? [];

        return Inertia::render('Export/TypeSelection', [
            'type' => [
                'id' => $type->id,
                'title' => $type->title,
            ],
            'columns' => $columns->map(fn ($col) => [
                'id' => $col->id,
                'key' => $col->key,
                'label' => $col->label,
                'include' => true,
            ])->values()->all(),
        ]);
    }

    /**
     * Store export selection and download file for type
     */
    public function typeExport(Request $request, Type $type, ProjectExportService $service)
    {
        $this->authorize('export', $type);

        $validated = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'required|integer',
            'format' => 'required|in:xlsx,csv,tsv',
        ]);

        $template = $type->template;
        if (!$template) {
            return redirect()->back()->withErrors(['error' => 'Template not found']);
        }

        $selectedColumnIds = $this->filterColumns($validated['columns'], $template);

        return $service->exportCustomByType($type, $validated['format'], $selectedColumnIds, [], $request->user());
    }

    private function filterColumns(array $columnIds, $template): array
    {
        $allowed = $template->columns()->pluck('id')->all();
        $filtered = array_values(array_intersect($columnIds, $allowed));
        if (! $filtered) {
            abort(422, 'No valid columns selected for export.');
        }

        return $filtered;
    }
}

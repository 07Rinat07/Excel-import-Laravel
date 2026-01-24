<?php

namespace App\Services\Export;

use App\Exports\ProjectValuesExport;
use App\Models\ExcelTemplate;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProjectExportService
{
    public function exportByProject(Project $project, string $format): BinaryFileResponse
    {
        $template = $this->resolveTemplate($project->template_id, $project->type_id);
        $projects = collect([$project]);

        return $this->download($projects, $template, $format, "project-{$project->id}");
    }

    public function exportByTask(Task $task, string $format): BinaryFileResponse
    {
        $template = $this->resolveTemplate($task->template_id, $task->type_id);
        $projects = Project::query()
            ->where('task_id', $task->id)
            ->latest('id')
            ->get();

        return $this->download($projects, $template, $format, "task-{$task->id}");
    }

    public function exportByType(Type $type, string $format, ?User $user = null): BinaryFileResponse
    {
        $template = $this->resolveTemplate(null, $type->id);
        $projectsQuery = Project::query()
            ->where('type_id', $type->id)
            ->latest('id');

        if ($user && ! $user->isAdmin()) {
            $projectsQuery = $projectsQuery->visibleTo($user);
        }

        $projects = $projectsQuery->get();

        return $this->download($projects, $template, $format, "type-{$type->id}");
    }

    private function resolveTemplate(?int $templateId, ?int $typeId): ExcelTemplate
    {
        if ($templateId) {
            $template = ExcelTemplate::with('columns')->find($templateId);
            if ($template) {
                return $template;
            }
        }

        return ExcelTemplate::with('columns')
            ->where('type_id', $typeId)
            ->where('is_active', true)
            ->latest('id')
            ->firstOrFail();
    }

    private function download(Collection $projects, ExcelTemplate $template, string $format, string $prefix): BinaryFileResponse
    {
        $columns = $template->columns()->orderBy('position')->get();
        $columnIds = $columns->pluck('id')->all();

        $projects->load(['values' => function ($query) use ($columnIds) {
            $query->whereIn('template_column_id', $columnIds);
        }]);

        $headings = $columns->pluck('label')->all();
        $rows = $projects->map(function (Project $project) use ($columns) {
            $values = $project->values->keyBy('template_column_id');

            return $columns->map(function ($column) use ($values) {
                return optional($values->get($column->id))->value;
            })->all();
        });

        $filename = $prefix.'.'.$this->normalizeFormat($format);
        $writerType = $this->writerType($format);

        return Excel::download(new ProjectValuesExport($headings, $rows), $filename, $writerType);
    }

    private function normalizeFormat(string $format): string
    {
        $format = strtolower($format);
        if ($format === 'csv') return 'csv';
        if ($format === 'tsv') return 'tsv';
        return 'xlsx';
    }

    private function writerType(string $format): string
    {
        $format = strtolower($format);
        if ($format === 'csv') return Excel::CSV;
        if ($format === 'tsv') return Excel::TSV;
        return Excel::XLSX;
    }
}

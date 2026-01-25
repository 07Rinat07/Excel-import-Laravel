<?php

namespace App\Services\Export;

use App\Exports\ProjectValuesExport;
use App\Exports\ProjectValuesMultiSheetExport;
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

    public function exportCustomByTask(Task $task, string $format, array $columnIds, array $labels = []): BinaryFileResponse
    {
        $template = $this->resolveTemplate($task->template_id, $task->type_id);
        $projects = Project::query()
            ->where('task_id', $task->id)
            ->latest('id')
            ->get();

        return $this->downloadCustom($projects, $template, $format, "task-{$task->id}-custom", $columnIds, $labels);
    }

    public function exportCustomByType(Type $type, string $format, array $columnIds, array $labels = [], ?User $user = null): BinaryFileResponse
    {
        $template = $this->resolveTemplate(null, $type->id);
        $projectsQuery = Project::query()
            ->where('type_id', $type->id)
            ->latest('id');

        if ($user && ! $user->isAdmin()) {
            $projectsQuery = $projectsQuery->visibleTo($user);
        }

        $projects = $projectsQuery->get();

        return $this->downloadCustom($projects, $template, $format, "type-{$type->id}-custom", $columnIds, $labels);
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
        $projects = $this->normalizeProjects($projects);
        $columns = $template->columns()->orderBy('position')->get();
        $columnIds = $columns->pluck('id')->all();

        $filename = $prefix.'.'.$this->normalizeFormat($format);
        $writerType = $this->writerType($format);

        if ($this->supportsMultipleSheets($format)) {
            $sheets = $this->buildSheets($projects, $columns, $columnIds);
            if (count($sheets) > 1) {
                return Excel::download(new ProjectValuesMultiSheetExport($sheets), $filename, $writerType);
            }
        }

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

        return Excel::download(new ProjectValuesExport($headings, $rows), $filename, $writerType);
    }

    private function downloadCustom(Collection $projects, ExcelTemplate $template, string $format, string $prefix, array $columnIds, array $labels): BinaryFileResponse
    {
        $projects = $this->normalizeProjects($projects);
        $columnsById = $template->columns()->whereIn('id', $columnIds)->get()->keyBy('id');
        $orderedColumns = collect($columnIds)
            ->map(fn ($id) => $columnsById->get($id))
            ->filter();

        $orderedIds = $orderedColumns->pluck('id')->all();

        $filename = $prefix.'.'.$this->normalizeFormat($format);
        $writerType = $this->writerType($format);

        if ($this->supportsMultipleSheets($format)) {
            $sheets = $this->buildSheets($projects, $orderedColumns, $orderedIds, $labels);
            if (count($sheets) > 1) {
                return Excel::download(new ProjectValuesMultiSheetExport($sheets), $filename, $writerType);
            }
        }

        $projects->load(['values' => function ($query) use ($orderedIds) {
            $query->whereIn('template_column_id', $orderedIds);
        }]);

        $headings = $orderedColumns->map(function ($column) use ($labels) {
            $custom = trim((string) ($labels[$column->id] ?? ''));
            return $custom !== '' ? $custom : $column->label;
        })->all();

        $rows = $projects->map(function (Project $project) use ($orderedColumns) {
            $values = $project->values->keyBy('template_column_id');

            return $orderedColumns->map(function ($column) use ($values) {
                return optional($values->get($column->id))->value;
            })->all();
        });

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
        if ($format === 'csv') return \Maatwebsite\Excel\Excel::CSV;
        if ($format === 'tsv') return \Maatwebsite\Excel\Excel::TSV;
        return \Maatwebsite\Excel\Excel::XLSX;
    }

    private function normalizeProjects(Collection $projects): \Illuminate\Database\Eloquent\Collection
    {
        if ($projects instanceof \Illuminate\Database\Eloquent\Collection) {
            return $projects;
        }

        return new \Illuminate\Database\Eloquent\Collection($projects->all());
    }

    private function supportsMultipleSheets(string $format): bool
    {
        return strtolower($format) === 'xlsx';
    }

    private function buildSheets(
        \Illuminate\Database\Eloquent\Collection $projects,
        \Illuminate\Support\Collection $columns,
        array $columnIds,
        array $labels = []
    ): array {
        $projects->load(['values' => function ($query) use ($columnIds) {
            $query->whereIn('template_column_id', $columnIds);
        }]);

        $grouped = $projects->groupBy(function (Project $project) {
            return $project->sheet_name ?: 'Sheet1';
        });

        $groups = $grouped->map(function ($rows, $sheetName) {
            $sheetIndex = $rows->min('sheet_index');
            return [
                'name' => (string) $sheetName,
                'index' => is_numeric($sheetIndex) ? (int) $sheetIndex : 0,
                'rows' => $rows,
            ];
        })->values();

        $groups = $groups->sortBy(function (array $group) {
            return sprintf('%05d-%s', $group['index'], $group['name']);
        })->values();

        $headings = $columns->map(function ($column) use ($labels) {
            $custom = trim((string) ($labels[$column->id] ?? ''));
            return $custom !== '' ? $custom : $column->label;
        })->all();

        $usedTitles = [];
        $sheets = [];

        foreach ($groups as $group) {
            $title = $this->normalizeSheetTitle($group['name'], $usedTitles);
            $rows = $group['rows']->map(function (Project $project) use ($columns) {
                $values = $project->values->keyBy('template_column_id');

                return $columns->map(function ($column) use ($values) {
                    return optional($values->get($column->id))->value;
                })->all();
            });

            $sheets[] = [
                'title' => $title,
                'headings' => $headings,
                'rows' => $rows,
            ];
        }

        return $sheets;
    }

    private function normalizeSheetTitle(string $title, array &$usedTitles): string
    {
        $clean = preg_replace('/[\\[\\]\\*\\?\\/\\\\:]/', '-', $title);
        $clean = trim((string) $clean);
        if ($clean === '') {
            $clean = 'Sheet';
        }
        $clean = mb_substr($clean, 0, 31);

        $candidate = $clean;
        $suffix = 2;
        while (in_array($candidate, $usedTitles, true)) {
            $base = mb_substr($clean, 0, 28);
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }
        $usedTitles[] = $candidate;

        return $candidate;
    }
}

<?php

namespace App\Services\Export;

use App\Exports\ProjectValuesMultiSheetQueryExport;
use App\Exports\ProjectValuesQueryExport;
use App\Models\ExcelTemplate;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use App\Exports\ProjectValuesViewExport;
use App\Jobs\ProcessExportJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProjectExportService
{
    private const QUEUE_THRESHOLD = 1000; // Number of projects to trigger queuing
    private const QUEUE_DELAY = 5; // Delay in seconds before the job is processed
    private const QUEUE_CONNECTION = 'database'; // Or 'redis', 'sqs', etc.
    private const QUEUE_NAME = 'exports'; // Specific queue name

    public function exportFromView(string $view, array $data, string $format, string $filenamePrefix, array $columnWidths = [], bool $queue = false): BinaryFileResponse
    {
        $filename = $filenamePrefix . '.' . $this->normalizeFormat($format);
        $writerType = $this->writerType($format);

        if ($queue) {
            ProcessExportJob::dispatch($view, $data, $format, $filenamePrefix, $columnWidths)
                ->delay(now()->addSeconds(self::QUEUE_DELAY))
                ->onConnection(self::QUEUE_CONNECTION)
                ->onQueue(self::QUEUE_NAME);

            // Return a response indicating that the export has been queued
            abort(202, 'Export has been queued and will be processed shortly.');
        }

        return Excel::download(new ProjectValuesViewExport($view, $data, $columnWidths), $filename, $writerType);
    }

    public function exportByProject(Project $project, string $format): BinaryFileResponse
    {
        $template = $this->resolveTemplate($project->template_id, $project->type_id);
        $projectsQuery = Project::query()
            ->whereKey($project->id);

        return $this->downloadQuery($projectsQuery, $template, $format, "project-{$project->id}");
    }

    public function exportCustomByProject(Project $project, string $format, array $columnIds, array $labels = []): BinaryFileResponse
    {
        $template = $this->resolveTemplate($project->template_id, $project->type_id);
        $projectsQuery = Project::query()
            ->whereKey($project->id);

        return $this->downloadCustomQuery($projectsQuery, $template, $format, "project-{$project->id}-custom", $columnIds, $labels);
    }

    public function exportByTask(Task $task, string $format, ?string $sheetName = null, ?int $sheetIndex = null): BinaryFileResponse
    {
        $template = $this->resolveTemplate($task->template_id, $task->type_id);
        $projectsQuery = Project::query()
            ->where('task_id', $task->id)
            ->latest('id');
        $projectsQuery = $this->applySheetFilter($projectsQuery, $sheetName, $sheetIndex);

        return $this->downloadQuery($projectsQuery, $template, $format, "task-{$task->id}");
    }

    public function exportByType(Type $type, string $format, ?User $user = null, ?int $userId = null, ?string $sheetName = null, ?int $sheetIndex = null): BinaryFileResponse
    {
        $template = $this->resolveTemplate(null, $type->id);
        $projectsQuery = Project::query()
            ->where('type_id', $type->id)
            ->latest('id');

        $projectsQuery = $this->applyUserFilter($projectsQuery, $user, $userId);
        $projectsQuery = $this->applySheetFilter($projectsQuery, $sheetName, $sheetIndex);

        return $this->downloadQuery($projectsQuery, $template, $format, "type-{$type->id}");
    }

    public function exportCustomByTask(Task $task, string $format, array $columnIds, array $labels = [], ?string $sheetName = null, ?int $sheetIndex = null): BinaryFileResponse
    {
        $template = $this->resolveTemplate($task->template_id, $task->type_id);
        $projectsQuery = Project::query()
            ->where('task_id', $task->id)
            ->latest('id');
        $projectsQuery = $this->applySheetFilter($projectsQuery, $sheetName, $sheetIndex);

        return $this->downloadCustomQuery($projectsQuery, $template, $format, "task-{$task->id}-custom", $columnIds, $labels);
    }

    public function exportCustomByType(Type $type, string $format, array $columnIds, array $labels = [], ?User $user = null, ?int $userId = null, ?string $sheetName = null, ?int $sheetIndex = null): BinaryFileResponse
    {
        $template = $this->resolveTemplate(null, $type->id);
        $projectsQuery = Project::query()
            ->where('type_id', $type->id)
            ->latest('id');

        $projectsQuery = $this->applyUserFilter($projectsQuery, $user, $userId);
        $projectsQuery = $this->applySheetFilter($projectsQuery, $sheetName, $sheetIndex);

        return $this->downloadCustomQuery($projectsQuery, $template, $format, "type-{$type->id}-custom", $columnIds, $labels);
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

    private function downloadQuery(Builder $query, ExcelTemplate $template, string $format, string $prefix): BinaryFileResponse
    {
        $columns = $template->columns()->orderBy('position')->get();
        $columnIds = $columns->pluck('id')->all();

        return $this->downloadQueryWithColumns($query, $columns, $columnIds, $format, $prefix);
    }

    private function downloadCustomQuery(Builder $query, ExcelTemplate $template, string $format, string $prefix, array $columnIds, array $labels): BinaryFileResponse
    {
        $columnsById = $template->columns()->whereIn('id', $columnIds)->get()->keyBy('id');
        $orderedColumns = collect($columnIds)
            ->map(fn ($id) => $columnsById->get($id))
            ->filter()
            ->values();

        return $this->downloadQueryWithColumns($query, $orderedColumns, $orderedColumns->pluck('id')->all(), $format, $prefix, $labels);
    }

    private function downloadQueryWithColumns(Builder $query, Collection $columns, array $columnIds, string $format, string $prefix, array $labels = []): BinaryFileResponse
    {
        $filename = $prefix.'.'.$this->normalizeFormat($format);
        $writerType = $this->writerType($format);

        if ($this->supportsMultipleSheets($format)) {
            $sheets = $this->buildQuerySheets($query, $columns, $columnIds, $labels);
            if (count($sheets) > 1) {
                return Excel::download(new ProjectValuesMultiSheetQueryExport($sheets), $filename, $writerType);
            }
        }

        $query = $this->applyValuesWith($query, $columnIds);

        return Excel::download(
            new ProjectValuesQueryExport($query, $columns, $labels),
            $filename,
            $writerType
        );
    }

    private function applyValuesWith(Builder $query, array $columnIds): Builder
    {
        return $query->with(['values' => function ($valueQuery) use ($columnIds) {
            $valueQuery->whereIn('template_column_id', $columnIds);
        }]);
    }

    public function normalizeFormat(string $format): string
    {
        $format = strtolower($format);
        if ($format === 'csv') return 'csv';
        if ($format === 'tsv') return 'tsv';
        return 'xlsx';
    }

    public function writerType(string $format): string
    {
        $format = strtolower($format);
        if ($format === 'csv') return \Maatwebsite\Excel\Excel::CSV;
        if ($format === 'tsv') return \Maatwebsite\Excel\Excel::TSV;
        return \Maatwebsite\Excel\Excel::XLSX;
    }

    private function supportsMultipleSheets(string $format): bool
    {
        return strtolower($format) === 'xlsx';
    }

    private function buildQuerySheets(Builder $query, Collection $columns, array $columnIds, array $labels = []): array
    {
        $groups = $this->sheetGroups($query);
        if (count($groups) <= 1) {
            return [];
        }

        $usedTitles = [];
        $sheets = [];

        foreach ($groups as $group) {
            $title = $this->normalizeSheetTitle($group['name'], $usedTitles);
            $sheetQuery = clone $query;
            if ($group['raw_name'] === null) {
                $sheetQuery->whereNull('sheet_name');
            } else {
                $sheetQuery->where('sheet_name', $group['raw_name']);
            }
            $sheetQuery = $this->applyValuesWith($sheetQuery, $columnIds);

            $sheets[] = new ProjectValuesQueryExport($sheetQuery, $columns, $labels, $title);
        }

        return $sheets;
    }

    private function sheetGroups(Builder $query): array
    {
        $groupQuery = (clone $query)->reorder();

        $rows = $groupQuery
            ->selectRaw('sheet_name, MIN(sheet_index) as sheet_index')
            ->groupBy('sheet_name')
            ->get();

        return $rows
            ->map(function ($row) {
                $rawName = $row->sheet_name ?? null;
                $name = ($rawName === null || $rawName === '') ? 'Sheet1' : $rawName;
                $index = $row->sheet_index !== null ? (int) $row->sheet_index : 0;

                return [
                    'raw_name' => $rawName,
                    'name' => $name,
                    'index' => $index,
                ];
            })
            ->sortBy(function (array $group) {
                return sprintf('%05d-%s', $group['index'], $group['name']);
            })
            ->values()
            ->all();
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

    private function applyUserFilter(Builder $query, ?User $actor, ?int $userId): Builder
    {
        if ($userId) {
            return $query->whereHas('task', function (Builder $taskQuery) use ($userId) {
                $taskQuery->where('user_id', $userId);
            });
        }

        if ($actor && ! $actor->isAdmin()) {
            return $query->visibleTo($actor);
        }

        return $query;
    }

    private function applySheetFilter(Builder $query, ?string $sheetName, ?int $sheetIndex): Builder
    {
        $sheetName = is_string($sheetName) ? trim($sheetName) : null;
        if ($sheetName !== null && $sheetName !== '') {
            return $query->where('sheet_name', $sheetName);
        }

        if ($sheetIndex !== null) {
            return $query->where('sheet_index', $sheetIndex);
        }

        return $query;
    }
}

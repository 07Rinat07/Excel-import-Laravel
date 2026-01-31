<?php

namespace App\Services;

use App\Imports\UniversalProjectImport;
use App\Models\ExcelTemplate;
use App\Models\Project;
use App\Models\ProjectValue;
use App\Models\Task;
use App\Services\Import\ImportFailureRecorder;
use Maatwebsite\Excel\Facades\Excel;

class ProjectImportService implements ProjectImportServiceInterface
{
    private ImportFailureRecorder $failureRecorder;

    public function __construct(ImportFailureRecorder $failureRecorder)
    {
        $this->failureRecorder = $failureRecorder;
    }

    public function import(Task $task, string $path): void
    {
        $importer = new UniversalProjectImport($task, $this->failureRecorder);
        $disk = config('imports.disk', 'public');
        Excel::import($importer, $path, $disk);
    }

    public function reimportCorrectedRows(Task $task, array $correctedRows): void
    {
        if (! $correctedRows) {
            return;
        }

        $template = ExcelTemplate::with('columns')->find($task->template_id);
        if (! $template) {
            return;
        }

        $columnsById = $template->columns->keyBy('id');
        $mappedColumns = [];

        if (is_array($task->column_map) && $task->column_map) {
            foreach ($task->column_map as $columnId) {
                $columnId = (int) $columnId;
                if (isset($columnsById[$columnId])) {
                    $mappedColumns[$columnId] = $columnsById[$columnId];
                }
            }
        } else {
            foreach ($columnsById as $column) {
                $mappedColumns[$column->id] = $column;
            }
        }

        if (! $mappedColumns) {
            return;
        }

        $now = now();
        $failedRowIds = [];
        $firstColumn = reset($mappedColumns) ?: null;

        foreach ($correctedRows as $row) {
            $data = $row['data'] ?? [];
            $rowNumber = $row['row_number'] ?? null;
            if (! empty($row['id'])) {
                $failedRowIds[] = (int) $row['id'];
            }

            $project = Project::create([
                'type_id' => $task->type_id,
                'task_id' => $task->id,
                'template_id' => $template->id,
                'row_index' => $rowNumber,
                'sheet_name' => $task->sheet ?? 'Sheet1',
                'sheet_index' => $task->selected_sheet_index ?? 0,
                'title' => (string) ($firstColumn ? ($data[$firstColumn->key] ?? null) : null) ?: ('Row '.$rowNumber),
                'created_at_time' => $now->format('Y-m-d'),
                'contracted_at' => $now->format('Y-m-d'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $valueRows = [];
            foreach ($mappedColumns as $column) {
                $valueRows[] = [
                    'project_id' => $project->id,
                    'template_column_id' => $column->id,
                    'value' => $data[$column->key] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($valueRows) {
                ProjectValue::insert($valueRows);
            }
        }

        if ($failedRowIds) {
            $task->failedRows()->whereIn('id', $failedRowIds)->delete();
        }

        $task->refresh();
        $task->update([
            'imported_rows' => ($task->imported_rows ?? 0) + count($correctedRows),
        ]);

        if ($task->failedRows()->count() === 0 && $task->status === Task::STATUS_PROCESS) {
            $task->update(['status' => Task::STATUS_SUCCESS]);
        }
    }
}

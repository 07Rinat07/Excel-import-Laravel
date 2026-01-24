<?php

namespace App\Services\Import;

use App\Models\FailedRow;
use App\Models\Task;

class ImportFailureRecorder
{
    /**
     * @param  array<int, \Maatwebsite\Excel\Validators\Failure>  $failures
     * @param  array<string, string>  $attributesMap
     */
    public function recordFailures(array $failures, array $attributesMap, Task $task): void
    {
        $rows = [];
        $now = now();

        foreach ($failures as $failure) {
            foreach ($failure->errors() as $error) {
                $attribute = $attributesMap[$failure->attribute()] ?? $failure->attribute();
                $rows[] = [
                    'key' => $attribute,
                    'row' => $failure->row(),
                    'message' => $error,
                    'task_id' => $task->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! $rows) {
            return;
        }

        FailedRow::insert($rows);
        $task->update(['status' => Task::STATUS_ERROR]);
    }

    /**
     * @param  array<int, array{row:int, key:string, message:string}>  $failures
     */
    public function recordCustomFailures(array $failures, Task $task): void
    {
        if (! $failures) {
            return;
        }

        $rows = [];
        $now = now();

        foreach ($failures as $failure) {
            $rows[] = [
                'key' => $failure['key'],
                'row' => $failure['row'],
                'message' => $failure['message'],
                'task_id' => $task->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        FailedRow::insert($rows);
        $task->update(['status' => Task::STATUS_ERROR]);
    }
}

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
        $grouped = [];
        $now = now();

        foreach ($failures as $failure) {
            $rowNumber = $failure->row();
            $attribute = $attributesMap[$failure->attribute()] ?? $failure->attribute();
            $errors = $failure->errors();
            $rowData = method_exists($failure, 'values') ? (array) $failure->values() : [];

            if (! isset($grouped[$rowNumber])) {
                $grouped[$rowNumber] = [
                    'row_number' => $rowNumber,
                    'row' => $rowNumber,
                    'data' => $rowData ?: null,
                    'errors' => [],
                ];
            }

            $grouped[$rowNumber]['errors'][$attribute] = $errors;
        }

        foreach ($grouped as $rowNumber => $payload) {
            $firstKey = array_key_first($payload['errors']);
            $firstMessage = null;
            if ($firstKey !== null) {
                $messages = $payload['errors'][$firstKey] ?? [];
                $firstMessage = is_array($messages) ? ($messages[0] ?? null) : $messages;
            }

            $rows[] = [
                'task_id' => $task->id,
                'row' => $payload['row'],
                'row_number' => $payload['row_number'],
                'key' => $firstKey,
                'message' => $firstMessage,
                'data' => $payload['data'],
                'errors' => $payload['errors'],
                'error_messages' => $payload['errors'],
                'is_valid' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! $rows) {
            return;
        }

        FailedRow::insert($rows);
        $task->update(['status' => Task::STATUS_ERROR]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $failures
     */
    public function recordCustomFailures(array $failures, Task $task): void
    {
        if (! $failures) {
            return;
        }

        $rows = [];
        $now = now();

        foreach ($failures as $failure) {
            $rowNumber = $failure['row_number'] ?? $failure['row'] ?? null;
            $errors = $failure['errors'] ?? null;
            $errorMessages = $failure['error_messages'] ?? $errors;

            $key = $failure['key'] ?? (is_array($errors) ? array_key_first($errors) : null);
            $message = $failure['message'] ?? null;
            if ($message === null && is_array($errors) && $key !== null) {
                $messages = $errors[$key] ?? [];
                $message = is_array($messages) ? ($messages[0] ?? null) : $messages;
            }

            $rows[] = [
                'task_id' => $task->id,
                'row' => $failure['row'] ?? $rowNumber,
                'row_number' => $rowNumber,
                'key' => $key,
                'message' => $message,
                'data' => $failure['data'] ?? null,
                'errors' => $errors,
                'error_messages' => $errorMessages,
                'is_valid' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        FailedRow::insert($rows);
        $task->update(['status' => Task::STATUS_ERROR]);
    }
}

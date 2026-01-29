<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FailedRow;
use App\Models\Task;
use App\Services\DataValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FailedRowController extends Controller
{
    public function __construct(
        private DataValidationService $validationService
    ) {}

    /**
     * Get all failed rows for a task
     */
    public function getFailedRows(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $failedRows = $task->failedRows()
            ->select(['id', 'row', 'errors', 'corrected_data', 'is_corrected', 'message', 'created_at'])
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $failedRows,
            'summary' => [
                'total_failed' => $task->failedRows()->count(),
                'total_corrected' => $task->failedRows()->where('is_corrected', true)->count(),
                'total_pending' => $task->failedRows()->where('is_corrected', false)->count(),
            ],
        ]);
    }

    /**
     * Update a single failed row with corrected data
     */
    public function updateFailedRow(Request $request, FailedRow $failedRow): JsonResponse
    {
        $this->authorize('update', $failedRow->task);

        $validated = $request->validate([
            'corrected_data' => 'required|array',
        ]);

        if (! $failedRow->task->template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found for task',
            ], 422);
        }

        $templateColumns = $failedRow->task->template->columns()
            ->select('key', 'label', 'validation_rules', 'data_type', 'is_required')
            ->get()
            ->map(fn($col) => $this->normalizeColumnRules($col->key, $col->label, $col->validation_rules, $col->data_type, $col->is_required))
            ->toArray();

        // Validate corrected data
        $validation = $this->validationService->validateRow(
            $validated['corrected_data'],
            $templateColumns
        );

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Corrected data failed validation',
                'errors' => $validation['errors'],
            ], 422);
        }

        $failedRow->markCorrected($validated['corrected_data']);

        return response()->json([
            'success' => true,
            'message' => 'Row corrected successfully',
            'data' => $failedRow,
        ]);
    }

    /**
     * Bulk update multiple failed rows
     */
    public function bulkUpdateFailedRows(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|integer|exists:failed_rows,id',
            'rows.*.corrected_data' => 'required|array',
        ]);

        if (! $task->template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found for task',
            ], 422);
        }

        $templateColumns = $task->template->columns()
            ->select('key', 'label', 'validation_rules', 'data_type', 'is_required')
            ->get()
            ->map(fn($col) => $this->normalizeColumnRules($col->key, $col->label, $col->validation_rules, $col->data_type, $col->is_required))
            ->toArray();

        $updated = 0;
        $failed = [];

        foreach ($validated['rows'] as $rowData) {
            $failedRow = FailedRow::find($rowData['id']);

            // Validate corrected data
            $validation = $this->validationService->validateRow(
                $rowData['corrected_data'],
                $templateColumns
            );

            if (!$validation['valid']) {
                $failed[] = [
                    'id' => $rowData['id'],
                    'errors' => $validation['errors'],
                ];
            } else {
                $failedRow->markCorrected($rowData['corrected_data']);
                $updated++;
            }
        }

        return response()->json([
            'success' => count($failed) === 0,
            'message' => "$updated rows corrected, " . count($failed) . " failed validation",
            'updated' => $updated,
            'failed' => $failed,
        ], count($failed) === 0 ? 200 : 207);
    }

    /**
     * Re-import corrected rows
     */
    public function reimportCorrectedRows(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $correctedRows = $task->failedRows()
            ->where('is_corrected', true)
            ->get();

        if ($correctedRows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No corrected rows found to re-import',
            ], 404);
        }

        // This would trigger the actual import job
        // Implementation depends on your import job class
        dispatch(new \App\Jobs\ImportProjectExcelFileJob(
            $task->file?->path ?? '',
            $task,
            $correctedRows->map(function ($row) {
                return [
                    'id' => $row->id,
                    'row_number' => $row->row_number ?? $row->row,
                    'data' => $row->getDataForReimport(),
                ];
            })->toArray(),
            true
        ));

        return response()->json([
            'success' => true,
            'message' => 'Corrected rows queued for re-import',
            'count' => $correctedRows->count(),
        ]);
    }

    /**
     * Get single failed row details
     */
    public function show(FailedRow $failedRow): JsonResponse
    {
        $this->authorize('view', $failedRow->task);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $failedRow->id,
                'original_data' => $failedRow->getOriginalData(),
                'corrected_data' => $failedRow->corrected_data,
                'errors' => $failedRow->errors,
                'is_corrected' => $failedRow->is_corrected,
                'message' => $failedRow->message,
                'created_at' => $failedRow->created_at,
            ],
        ]);
    }

    /**
     * Delete a failed row (mark as ignored)
     */
    public function destroy(FailedRow $failedRow): JsonResponse
    {
        $this->authorize('delete', $failedRow->task);

        $failedRow->delete();

        return response()->json([
            'success' => true,
            'message' => 'Failed row deleted',
        ]);
    }

    private function normalizeColumnRules(string $key, string $label, mixed $validationRules, mixed $dataType, mixed $isRequired): array
    {
        $rules = is_array($validationRules) ? $validationRules : [];

        if ($isRequired && ! in_array('required', $rules, true)) {
            $rules[] = 'required';
        }

        $typeRule = match (strtolower((string) $dataType)) {
            'number' => 'numeric',
            'integer' => 'integer',
            'date' => 'date',
            'boolean' => 'boolean',
            default => null,
        };
        if ($typeRule) {
            $rules[] = $typeRule;
        }

        if (! in_array('required', $rules, true)) {
            $rules[] = 'nullable';
        }

        return [
            'key' => $key,
            'label' => $label,
            'validation_rules' => $rules,
        ];
    }
}

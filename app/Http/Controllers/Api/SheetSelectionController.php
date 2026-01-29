<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Task;
use App\Services\SheetProcessingService;
use App\Services\SmartMappingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SheetSelectionController extends Controller
{
    public function __construct(
        private SheetProcessingService $sheetService,
        private SmartMappingService $mappingService
    ) {}

    /**
     * Get available sheets from an uploaded file
     */
    public function getAvailableSheets(Request $request, File $file): JsonResponse
    {
        $path = Storage::disk('public')->path($file->path);
        $sheets = $this->sheetService->getAvailableSheets($path);

        if (empty($sheets)) {
            return response()->json([
                'success' => false,
                'message' => 'No sheets found in file',
            ], 400);
        }

        // Get statistics for each sheet
        $sheetsWithStats = array_map(function ($sheet) use ($path) {
            $stats = $this->sheetService->getSheetStatistics($path, $sheet['index']);
            return array_merge($sheet, $stats ?? []);
        }, $sheets);

        // Recommend best sheet
        $bestSheetIndex = $this->sheetService->findBestSheet($path);

        return response()->json([
            'success' => true,
            'data' => [
                'sheets' => $sheetsWithStats,
                'recommended_sheet_index' => $bestSheetIndex,
            ],
        ]);
    }

    /**
     * Get headers from a specific sheet (for preview/mapping)
     */
    public function getSheetHeaders(Request $request, File $file): JsonResponse
    {
        $validated = $request->validate([
            'sheet_index' => 'required|integer|min:0',
        ]);

        $path = Storage::disk('public')->path($file->path);
        if (!$this->sheetService->validateSheetIndex($path, $validated['sheet_index'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid sheet index',
            ], 400);
        }

        $headers = $this->sheetService->getSheetHeaders(
            $path,
            $validated['sheet_index']
        );

        return response()->json([
            'success' => true,
            'data' => [
                'headers' => $headers,
                'column_count' => count($headers),
            ],
        ]);
    }

    /**
     * Get preview data from sheet
     */
    public function getSheetPreview(Request $request, File $file): JsonResponse
    {
        $validated = $request->validate([
            'sheet_index' => 'required|integer|min:0',
            'rows_count' => 'integer|min:1|max:50',
        ]);

        $path = Storage::disk('public')->path($file->path);
        if (!$this->sheetService->validateSheetIndex($path, $validated['sheet_index'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid sheet index',
            ], 400);
        }

        $rowsCount = $validated['rows_count'] ?? 5;

        $headers = $this->sheetService->getSheetHeaders(
            $path,
            $validated['sheet_index']
        );

        $data = $this->sheetService->readSheetData(
            $path,
            $validated['sheet_index'],
            skipHeader: true,
            maxRows: $rowsCount
        );

        return response()->json([
            'success' => true,
            'data' => [
                'headers' => $headers,
                'rows' => $data,
                'rows_count' => count($data),
            ],
        ]);
    }

    /**
     * Select sheet and initialize task with mapping
     */
    public function selectSheetForImport(Request $request, File $file): JsonResponse
    {
        $validated = $request->validate([
            'sheet_index' => 'required|integer|min:0',
            'template_id' => 'required|integer|exists:excel_templates,id',
            'task_name' => 'nullable|string|max:255',
        ]);

        $path = Storage::disk('public')->path($file->path);
        if (!$this->sheetService->validateSheetIndex($path, $validated['sheet_index'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid sheet index',
            ], 400);
        }

        // Get headers and available sheets
        $headers = $this->sheetService->getSheetHeaders(
            $path,
            $validated['sheet_index']
        );

        $availableSheets = $this->sheetService->getAvailableSheets($path);

        $template = \App\Models\ExcelTemplate::find($validated['template_id']);
        if (! $template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found',
            ], 404);
        }

        // Get template columns for smart mapping
        $templateColumns = $template->columns()->get();

        // Perform smart mapping
        $mappingSuggestion = $this->mappingService->suggestMapping($headers, $templateColumns);

        // Create/update task
        $task = Task::updateOrCreate(
            ['file_id' => $file->id],
            [
                'name' => $validated['task_name'] ?? "Import - {$file->name}",
                'type' => 1,
                'template_id' => $validated['template_id'],
                'type_id' => $template->type_id,
                'user_id' => $request->user()->id,
                'status' => Task::STATUS_PENDING,
                'available_sheets' => $availableSheets,
                'selected_sheet_index' => $validated['sheet_index'],
                'sheet' => $availableSheets[$validated['sheet_index']]['name'] ?? null,
                'mapping' => $mappingSuggestion['mapping'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Sheet selected successfully',
            'data' => [
                'task_id' => $task->id,
                'selected_sheet' => $availableSheets[$validated['sheet_index']] ?? null,
                'mapping_suggestion' => $mappingSuggestion,
            ],
        ]);
    }
}

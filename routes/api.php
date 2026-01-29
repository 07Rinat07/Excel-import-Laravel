<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ProjectImportController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\TypeController;
use App\Http\Controllers\Api\FailedRowController;
use App\Http\Controllers\Api\SheetSelectionController;
use App\Http\Controllers\Api\EnhancedExportController;
use App\Http\Controllers\ProjectExportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum', 'not_blocked'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/health', HealthController::class);

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('api.tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('api.tasks.show');

    // Types and Templates
    Route::get('/types', [TypeController::class, 'index'])->name('api.types.index');
    Route::get('/templates', [TemplateController::class, 'index'])->name('api.templates.index');

    // Import/Export - with Rate Limiting (10 per minute per user)
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/projects/import', [ProjectImportController::class, 'store'])->name('api.projects.import');
        Route::post('/exports', [ExportController::class, 'store'])->name('api.exports.store');
    });

    // Export retrieval - higher limit (60 per minute)
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/exports/{id}', [ExportController::class, 'show'])->name('api.exports.show');
        Route::get('/exports/{id}/download', [ExportController::class, 'download'])->name('api.exports.download');

        // Enhanced Export (specific routes before generic export format)
        Route::get('/projects/{project}/export/formatted', [EnhancedExportController::class, 'exportFormattedData'])->name('api.export.formatted');
        Route::get('/projects/{project}/export/report', [EnhancedExportController::class, 'exportReport'])->name('api.export.report');
        Route::get('/projects/{project}/export/validated', [EnhancedExportController::class, 'exportWithValidation'])->name('api.export.validated');

        Route::get('/projects/{project}/export/{format}', [ProjectExportController::class, 'project'])->name('api.projects.export');
        Route::get('/tasks/{task}/export/{format}', [ProjectExportController::class, 'task'])->name('api.tasks.export');
        Route::get('/types/{type}/export/{format}', [ProjectExportController::class, 'type'])->name('api.types.export');
    });

    // Failed Rows Management - with Rate Limiting (30 per minute)
    Route::middleware('throttle:30,1')->group(function () {
        Route::get('/tasks/{task}/failed-rows', [FailedRowController::class, 'getFailedRows'])->name('api.failed_rows.index');
        Route::get('/failed-rows/{failedRow}', [FailedRowController::class, 'show'])->name('api.failed_rows.show');
        Route::patch('/failed-rows/{failedRow}', [FailedRowController::class, 'updateFailedRow'])->name('api.failed_rows.update');
        Route::post('/tasks/{task}/failed-rows/bulk-update', [FailedRowController::class, 'bulkUpdateFailedRows'])->name('api.failed_rows.bulk_update');
        Route::post('/tasks/{task}/failed-rows/reimport', [FailedRowController::class, 'reimportCorrectedRows'])->name('api.failed_rows.reimport');
        Route::delete('/failed-rows/{failedRow}', [FailedRowController::class, 'destroy'])->name('api.failed_rows.destroy');
    });

    // Sheet Selection & Multi-sheet Support
    Route::get('/files/{file}/sheets', [SheetSelectionController::class, 'getAvailableSheets'])->name('api.sheets.available');
    Route::post('/files/{file}/sheets/headers', [SheetSelectionController::class, 'getSheetHeaders'])->name('api.sheets.headers');
    Route::post('/files/{file}/sheets/preview', [SheetSelectionController::class, 'getSheetPreview'])->name('api.sheets.preview');
    Route::post('/files/{file}/sheets/select', [SheetSelectionController::class, 'selectSheetForImport'])->name('api.sheets.select');

});

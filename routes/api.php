<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ProjectImportController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\TypeController;
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
    Route::get('/tasks', [TaskController::class, 'index'])->name('api.tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('api.tasks.show');
    Route::get('/tasks/{task}/failed-rows', [TaskController::class, 'failedRows'])->name('api.tasks.failed_rows');
    Route::get('/types', [TypeController::class, 'index'])->name('api.types.index');
    Route::get('/templates', [TemplateController::class, 'index'])->name('api.templates.index');
    Route::post('/projects/import', [ProjectImportController::class, 'store'])->name('api.projects.import');
    Route::get('/projects/{project}/export/{format}', [ProjectExportController::class, 'project'])->name('api.projects.export');
    Route::get('/tasks/{task}/export/{format}', [ProjectExportController::class, 'task'])->name('api.tasks.export');
    Route::get('/types/{type}/export/{format}', [ProjectExportController::class, 'type'])->name('api.types.export');
});

<?php

use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\TemplateController as AdminTemplateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportSelectionController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectExportController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::post('/locale/{locale}', function (Request $request, string $locale) {
    if (! in_array($locale, ['en', 'ru'], true)) {
        abort(404);
    }

    $request->session()->put('locale', $locale);

    return redirect()->back();
})->name('locale.set');

Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [FeedbackController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('feedback.store');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'not_blocked'])->name('dashboard');

Route::middleware(['auth', 'not_blocked'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/projects', [ProjectController::class, 'index'])->name('project.index');
    Route::get('/projects/import', [ProjectController::class, 'import'])->name('project.import');
    Route::post('/projects/import', [ProjectController::class, 'importStore'])->name('project.import.store');
    Route::post('/projects/import/prepare', [ProjectController::class, 'importPrepare'])->name('project.import.prepare');
    Route::get('/projects/import/{task}/map', [ProjectController::class, 'importMap'])->name('project.import.map');
    Route::post('/projects/import/{task}/map', [ProjectController::class, 'importMapStore'])->name('project.import.map.store');
    Route::post('/projects/import/{task}/map-json', [ProjectController::class, 'importMapStoreJson'])->name('project.import.map.json');

    // Export selection routes
    Route::get('/projects/{project}/export-select', [ExportSelectionController::class, 'projectSelection'])->name('project.export.select');
    Route::post('/projects/{project}/export-select', [ExportSelectionController::class, 'projectExport'])->name('project.export.select.store');
    Route::get('/projects/{project}/export/{format}', [ProjectExportController::class, 'project'])->name('project.export');

    Route::get('/tasks', [TaskController::class, 'index'])->name('task.index');
    Route::get('/tasks/{task}/status', [TaskController::class, 'status'])->name('task.status');
    Route::get('/tasks/{task}/failed_list', [TaskController::class, 'failedList'])->name('task.failed_list');

    // Task export selection routes
    Route::get('/tasks/{task}/export-select', [ExportSelectionController::class, 'taskSelection'])->name('task.export.select');
    Route::post('/tasks/{task}/export-select', [ExportSelectionController::class, 'taskExport'])->name('task.export.select.store');
    Route::get('/tasks/{task}/export/{format}', [ProjectExportController::class, 'task'])->name('task.export');

    // Type export selection routes
    Route::get('/types/{type}/export-select', [ExportSelectionController::class, 'typeSelection'])->name('type.export.select');
    Route::post('/types/{type}/export-select', [ExportSelectionController::class, 'typeExport'])->name('type.export.select.store');
    Route::get('/types/{type}/export/{format}', [ProjectExportController::class, 'type'])->name('type.export');
});

Route::middleware(['auth', 'not_blocked', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/exports', [\App\Http\Controllers\Admin\ExportController::class, 'index'])->name('exports.index');
    Route::get('/exports/download', [\App\Http\Controllers\Admin\ExportController::class, 'download'])->name('exports.download');
    Route::get('/exports/download/{id}', [\App\Http\Controllers\Admin\ExportController::class, 'downloadFile'])->name('exports.download_file');
    Route::post('/exports/queue', [\App\Http\Controllers\Admin\ExportController::class, 'queue'])->name('exports.queue');
    Route::post('/exports/presets', [\App\Http\Controllers\Admin\ExportController::class, 'storePreset'])->name('exports.presets.store');
    Route::delete('/exports/presets/{preset}', [\App\Http\Controllers\Admin\ExportController::class, 'destroyPreset'])->name('exports.presets.destroy');
    Route::get('/types', [\App\Http\Controllers\Admin\TypeController::class, 'index'])->name('types.index');
    Route::post('/types', [\App\Http\Controllers\Admin\TypeController::class, 'store'])->name('types.store');
    Route::patch('/types/{type}', [\App\Http\Controllers\Admin\TypeController::class, 'update'])->name('types.update');
    Route::delete('/types/{type}', [\App\Http\Controllers\Admin\TypeController::class, 'destroy'])->name('types.destroy');
    Route::get('/data', [\App\Http\Controllers\Admin\DataController::class, 'index'])->name('data.index');
    Route::post('/data/rows', [\App\Http\Controllers\Admin\DataController::class, 'store'])->name('data.store');
    Route::patch('/data/rows', [\App\Http\Controllers\Admin\DataController::class, 'update'])->name('data.update');
    Route::delete('/data/rows', [\App\Http\Controllers\Admin\DataController::class, 'destroy'])->name('data.destroy');
    Route::get('/data/export', [\App\Http\Controllers\Admin\DataController::class, 'export'])->name('data.export');
    Route::get('/data/backup', [\App\Http\Controllers\Admin\DataController::class, 'backup'])->name('data.backup');
    Route::post('/data/cleanup', [\App\Http\Controllers\Admin\DataController::class, 'cleanup'])->name('data.cleanup');
    Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('feedback.index');
    Route::patch('/feedback/{message}/read', [AdminFeedbackController::class, 'markRead'])->name('feedback.read');
    Route::patch('/feedback/{message}/unread', [AdminFeedbackController::class, 'markUnread'])->name('feedback.unread');
    Route::post('/feedback/{message}/block', [AdminFeedbackController::class, 'blockUser'])->name('feedback.block');
    Route::delete('/feedback/{message}', [AdminFeedbackController::class, 'destroy'])->name('feedback.destroy');
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/block', [\App\Http\Controllers\Admin\UserController::class, 'block'])->name('users.block');
    Route::patch('/users/{user}/unblock', [\App\Http\Controllers\Admin\UserController::class, 'unblock'])->name('users.unblock');
    Route::patch('/users/{user}/make-admin', [\App\Http\Controllers\Admin\UserController::class, 'makeAdmin'])->name('users.make_admin');
    Route::patch('/users/{user}/revoke-admin', [\App\Http\Controllers\Admin\UserController::class, 'revokeAdmin'])->name('users.revoke_admin');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/templates', [AdminTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/{template}/edit', [AdminTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{template}', [AdminTemplateController::class, 'update'])->name('templates.update');
    Route::post('/templates/{template}/import-columns', [AdminTemplateController::class, 'importColumns'])->name('templates.import_columns');
});

require __DIR__.'/auth.php';

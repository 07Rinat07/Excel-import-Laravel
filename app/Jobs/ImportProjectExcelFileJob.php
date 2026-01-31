<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\ProjectImportServiceInterface;
use App\Services\SheetProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImportProjectExcelFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $path;

    private Task $task;

    private array $correctedRows;

    private bool $isReimport;

    /**
     * Create a new job instance.
     */
    public function __construct(string $path, Task $task, array $correctedRows = [], bool $isReimport = false)
    {
        $this->path = $path;
        $this->task = $task;
        $this->correctedRows = $correctedRows;
        $this->isReimport = $isReimport;
    }

    /**
     * Execute the job.
     */
    public function handle(ProjectImportServiceInterface $importService): void
    {
        $this->task->refresh();
        if ($this->task->status === Task::STATUS_SUCCESS && ! config('imports.allow_rerun_on_success', false)) {
            return;
        }

        $this->prepareForImport($this->task);

        try {
            if ($this->isReimport) {
                $importService->reimportCorrectedRows($this->task, $this->correctedRows);
            } else {
                $importService->import($this->task, $this->path);
            }
        } catch (Throwable $exception) {
            $this->task->update(['status' => Task::STATUS_ERROR]);
            report($exception);

            return;
        }

        $this->task->refresh();
        if ($this->task->status === Task::STATUS_PROCESS) {
            $this->task->update(['status' => Task::STATUS_SUCCESS]);
        }
    }

    private function prepareForImport(Task $task): void
    {
        if ($this->isReimport) {
            $task->update([
                'status' => Task::STATUS_PROCESS,
            ]);

            return;
        }

        $task->failedRows()->delete();
        $task->projects()->delete();
        $totalRows = 0;
        if ($task->file?->path) {
            $disk = config('imports.disk', 'public');
            $path = Storage::disk($disk)->path($task->file->path);
            $sheetIndex = $task->selected_sheet_index ?? 0;
            $sheetService = app(SheetProcessingService::class);
            $stats = $sheetService->getSheetStatistics($path, $sheetIndex);
            $headerRow = $sheetService->getHeaderRowIndex($path, $sheetIndex);
            $estimatedTotal = (int) ($stats['total_rows'] ?? 0) - $headerRow;
            $totalRows = max(0, $estimatedTotal);
        }
        $task->update([
            'status' => Task::STATUS_PROCESS,
            'total_rows' => $totalRows,
            'imported_rows' => 0,
        ]);
    }
}

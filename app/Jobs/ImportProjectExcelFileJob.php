<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\ProjectImportServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        $task->update([
            'status' => Task::STATUS_PROCESS,
            'total_rows' => 0,
            'imported_rows' => 0,
        ]);
    }
}

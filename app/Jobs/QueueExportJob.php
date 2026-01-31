<?php

namespace App\Jobs;

use App\Models\ExportLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Services\Export\ProjectExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Throwable;

class QueueExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $exportLogId;
    private string $sourceType;
    private int $sourceId;
    private string $format;
    private array $columnIds;
    private array $labels;
    private ?int $filterUserId;
    private ?string $sheetName;
    private ?int $sheetIndex;

    public function __construct(
        int $exportLogId,
        string $sourceType,
        int $sourceId,
        string $format,
        array $columnIds,
        array $labels = [],
        ?int $filterUserId = null,
        ?string $sheetName = null,
        ?int $sheetIndex = null
    ) {
        $this->exportLogId = $exportLogId;
        $this->sourceType = $sourceType;
        $this->sourceId = $sourceId;
        $this->format = $format;
        $this->columnIds = $columnIds;
        $this->labels = $labels;
        $this->filterUserId = $filterUserId;
        $this->sheetName = $sheetName;
        $this->sheetIndex = $sheetIndex;
    }

    public function handle(ProjectExportService $exportService): void
    {
        $log = ExportLog::find($this->exportLogId);
        if (! $log) {
            return;
        }

        $log->update(['status' => 'processing']);

        try {
            $path = $this->buildExportPath($log);

            match ($this->sourceType) {
                'project' => $exportService->storeCustomByProject(
                    Project::findOrFail($this->sourceId),
                    $this->format,
                    $this->columnIds,
                    $this->labels,
                    $path
                ),
                'task' => $exportService->storeCustomByTask(
                    Task::findOrFail($this->sourceId),
                    $this->format,
                    $this->columnIds,
                    $this->labels,
                    $this->sheetName,
                    $this->sheetIndex,
                    $path
                ),
                'type' => $exportService->storeCustomByType(
                    Type::findOrFail($this->sourceId),
                    $this->format,
                    $this->columnIds,
                    $this->labels,
                    null,
                    $this->filterUserId,
                    $this->sheetName,
                    $this->sheetIndex,
                    $path
                ),
                default => throw new \RuntimeException('Unsupported export source.'),
            };

            $log->update([
                'status' => 'success',
                'file_name' => $path,
                'failure_reason' => null,
            ]);
        } catch (Throwable $exception) {
            $log->update([
                'status' => 'failed',
                'failure_reason' => $exception->getMessage(),
            ]);
            report($exception);
        }
    }

    private function buildExportPath(ExportLog $log): string
    {
        $extension = $this->format;
        $suffix = Str::uuid()->toString();

        return 'queued/'.$this->sourceType.'-'.$this->sourceId.'-'.$log->id.'-'.$suffix.'.'.$extension;
    }
}

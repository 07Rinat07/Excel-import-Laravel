<?php

namespace App\Jobs;

use App\Exports\ProjectValuesViewExport;
use App\Services\Export\ProjectExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $view;
    private array $data;
    private string $format;
    private string $filenamePrefix;
    private array $columnWidths;

    public function __construct(string $view, array $data, string $format, string $filenamePrefix, array $columnWidths = [])
    {
        $this->view = $view;
        $this->data = $data;
        $this->format = $format;
        $this->filenamePrefix = $filenamePrefix;
        $this->columnWidths = $columnWidths;
    }

    public function handle(ProjectExportService $exportService)
    {
        $filename = $this->filenamePrefix . '.' . $exportService->normalizeFormat($this->format);
        $writerType = $exportService->writerType($this->format);

        Excel::store(new ProjectValuesViewExport($this->view, $this->data, $this->columnWidths), $filename, 'public', $writerType);

        // Optionally, log the export or notify the user
        // For example: ExportLog::create([...]);
    }
}

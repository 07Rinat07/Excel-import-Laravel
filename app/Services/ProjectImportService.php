<?php

namespace App\Services;

use App\Imports\UniversalProjectImport;
use App\Models\Task;
use App\Services\Import\ImportFailureRecorder;
use Maatwebsite\Excel\Facades\Excel;

class ProjectImportService implements ProjectImportServiceInterface
{
    private ImportFailureRecorder $failureRecorder;

    public function __construct(ImportFailureRecorder $failureRecorder)
    {
        $this->failureRecorder = $failureRecorder;
    }

    public function import(Task $task, string $path): void
    {
        $importer = new UniversalProjectImport($task, $this->failureRecorder);
        Excel::import($importer, $path, 'public');
    }
}

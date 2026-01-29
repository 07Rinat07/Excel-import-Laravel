<?php

namespace App\Services;

use App\Models\Task;

interface ProjectImportServiceInterface
{
    public function import(Task $task, string $path): void;

    /**
     * @param array<int, array<string, mixed>> $correctedRows
     */
    public function reimportCorrectedRows(Task $task, array $correctedRows): void;
}

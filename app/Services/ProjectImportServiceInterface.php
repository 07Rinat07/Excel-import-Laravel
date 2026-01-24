<?php

namespace App\Services;

use App\Models\Task;

interface ProjectImportServiceInterface
{
    public function import(Task $task, string $path): void;
}

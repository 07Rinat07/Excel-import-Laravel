<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\ProjectImportServiceInterface;
use Illuminate\Console\Command;

class TestDynamicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imports:test {task_id=1} {path=files/projects2.xlsx}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a project import against a local file for testing.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $taskId = (int) $this->argument('task_id');
        $path = (string) $this->argument('path');

        $task = Task::findOrFail($taskId);
        app(ProjectImportServiceInterface::class)->import($task, $path);

        return Command::SUCCESS;
    }
}

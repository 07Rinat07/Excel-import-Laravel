<?php

namespace App\Console\Commands;

use App\Imports\ProjectDynamicImport;
use App\Models\Task;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class TestDynamicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'for test and run myCommand';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Excel::import(new ProjectDynamicImport(Task::find(1)), 'files/projects2.xlsx', 'public');
        return Command::SUCCESS;
    }
}

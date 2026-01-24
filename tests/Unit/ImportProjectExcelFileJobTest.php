<?php

namespace Tests\Unit;

use App\Jobs\ImportProjectExcelFileJob;
use App\Models\File;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use App\Services\ProjectImportServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ImportProjectExcelFileJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sets_success_status_after_successful_import(): void
    {
        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        $file = File::create([
            'path' => 'files/projects.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'projects.xlsx',
        ]);
        $task = Task::create([
            'user_id' => $user->id,
            'file_id' => $file->id,
            'status' => Task::STATUS_PROCESS,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        $service = new class implements ProjectImportServiceInterface
        {
            public function import(Task $task, string $path): void
            {
                // no-op for testing success path
            }
        };

        $job = new ImportProjectExcelFileJob($file->path, $task);
        $job->handle($service);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Task::STATUS_SUCCESS,
        ]);
    }

    public function test_job_sets_error_status_on_exception(): void
    {
        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        $file = File::create([
            'path' => 'files/projects.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'projects.xlsx',
        ]);
        $task = Task::create([
            'user_id' => $user->id,
            'file_id' => $file->id,
            'status' => Task::STATUS_PROCESS,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        $service = new class implements ProjectImportServiceInterface
        {
            public function import(Task $task, string $path): void
            {
                throw new RuntimeException('Import failed.');
            }
        };

        $job = new ImportProjectExcelFileJob($file->path, $task);
        $job->handle($service);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Task::STATUS_ERROR,
        ]);
    }
}

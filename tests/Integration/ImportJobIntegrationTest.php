<?php

namespace Tests\Integration;

use App\Imports\UniversalProjectImport;
use App\Jobs\ImportProjectExcelFileJob;
use App\Models\File;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use App\Services\ProjectImportServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ImportJobIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_runs_import_service_and_marks_success(): void
    {
        Excel::fake();

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

        $service = app(ProjectImportServiceInterface::class);
        $job = new ImportProjectExcelFileJob($file->path, $task);
        $job->handle($service);

        Excel::assertImported($file->path, 'public', function ($import) {
            return $import instanceof UniversalProjectImport;
        });

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Task::STATUS_SUCCESS,
        ]);
    }
}

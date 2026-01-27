<?php

namespace Tests\Unit;

use App\Jobs\ImportProjectExcelFileJob;
use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use App\Models\FailedRow;
use App\Models\File;
use App\Models\Project;
use App\Models\ProjectValue;
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

    public function test_job_cleans_previous_import_data_before_retry(): void
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
            'status' => Task::STATUS_ERROR,
            'type' => 1,
            'type_id' => $type->id,
            'total_rows' => 5,
            'imported_rows' => 2,
        ]);
        $template = ExcelTemplate::create([
            'type_id' => $type->id,
            'name' => 'Template A',
            'header_hash' => 'hash',
            'is_active' => true,
        ]);
        $column = ExcelTemplateColumn::create([
            'template_id' => $template->id,
            'key' => 'name',
            'label' => 'Name',
            'data_type' => 'string',
            'is_required' => false,
            'position' => 0,
        ]);
        $project = Project::create([
            'type_id' => $type->id,
            'task_id' => $task->id,
            'template_id' => $template->id,
            'row_index' => 1,
            'title' => 'Old Project',
            'created_at_time' => now()->toDateString(),
            'contracted_at' => now()->toDateString(),
        ]);
        ProjectValue::create([
            'project_id' => $project->id,
            'template_column_id' => $column->id,
            'value' => 'Old Value',
        ]);
        $failedRow = FailedRow::create([
            'key' => 'name',
            'row' => 2,
            'message' => 'Required',
            'task_id' => $task->id,
        ]);

        $service = new class implements ProjectImportServiceInterface
        {
            public function import(Task $task, string $path): void
            {
                // no-op
            }
        };

        $job = new ImportProjectExcelFileJob($file->path, $task);
        $job->handle($service);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('project_values', ['project_id' => $project->id]);
        $this->assertDatabaseMissing('failed_rows', ['id' => $failedRow->id]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'total_rows' => 0,
            'imported_rows' => 0,
        ]);
    }

    public function test_job_skips_successful_task(): void
    {
        config(['imports.allow_rerun_on_success' => false]);

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
            'status' => Task::STATUS_SUCCESS,
            'type' => 1,
            'type_id' => $type->id,
        ]);
        $project = Project::create([
            'type_id' => $type->id,
            'task_id' => $task->id,
            'row_index' => 1,
            'title' => 'Existing Project',
            'created_at_time' => now()->toDateString(),
            'contracted_at' => now()->toDateString(),
        ]);

        $service = new class implements ProjectImportServiceInterface
        {
            public bool $called = false;

            public function import(Task $task, string $path): void
            {
                $this->called = true;
            }
        };

        $job = new ImportProjectExcelFileJob($file->path, $task);
        $job->handle($service);

        $this->assertFalse($service->called);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_job_allows_rerun_when_configured(): void
    {
        config(['imports.allow_rerun_on_success' => true]);

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
            'status' => Task::STATUS_SUCCESS,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        $service = new class implements ProjectImportServiceInterface
        {
            public bool $called = false;

            public function import(Task $task, string $path): void
            {
                $this->called = true;
            }
        };

        $job = new ImportProjectExcelFileJob($file->path, $task);
        $job->handle($service);

        $this->assertTrue($service->called);
    }
}

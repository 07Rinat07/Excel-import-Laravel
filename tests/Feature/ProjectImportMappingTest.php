<?php

namespace Tests\Feature;

use App\Jobs\ImportProjectExcelFileJob;
use App\Models\File;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ProjectImportMappingTest extends TestCase
{
    use RefreshDatabase;

    public function test_mapping_rejects_when_no_columns_selected(): void
    {
        Bus::fake();

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
            'status' => Task::STATUS_PENDING,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        $response = $this->actingAs($user)->post(route('project.import.map.store', $task), [
            'columns' => [
                [
                    'index' => 0,
                    'include' => false,
                    'name' => 'Title',
                    'data_type' => 'string',
                    'required' => false,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['mapping']);
        Bus::assertNotDispatched(ImportProjectExcelFileJob::class);
    }

    public function test_mapping_dispatches_job_and_updates_task(): void
    {
        Bus::fake();

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
            'status' => Task::STATUS_PENDING,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        $response = $this->actingAs($user)->post(route('project.import.map.store', $task), [
            'columns' => [
                [
                    'index' => 0,
                    'include' => true,
                    'name' => 'Title',
                    'data_type' => 'string',
                    'required' => true,
                ],
            ],
        ]);

        $response->assertRedirect(route('task.index'));
        $response->assertSessionHas('message');

        $task->refresh();
        $this->assertSame(Task::STATUS_PROCESS, $task->status);
        $this->assertNotNull($task->template_id);
        $this->assertNotEmpty($task->column_map);

        Bus::assertDispatched(ImportProjectExcelFileJob::class);
    }
}

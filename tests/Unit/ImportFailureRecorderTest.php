<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use App\Services\Import\ImportFailureRecorder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Validators\Failure;
use Tests\TestCase;

class ImportFailureRecorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_recorder_inserts_failed_rows_and_updates_task_status(): void
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

        $failure = new Failure(3, 'tip', ['Required']);
        $recorder = new ImportFailureRecorder;
        $recorder->recordFailures([$failure], ['tip' => 'Тип'], $task);

        $this->assertDatabaseHas('failed_rows', [
            'task_id' => $task->id,
            'row' => 3,
            'key' => 'Тип',
            'message' => 'Required',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Task::STATUS_ERROR,
        ]);
    }

    public function test_recorder_falls_back_to_attribute_name(): void
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

        $failure = new Failure(4, 'unknown_field', ['Invalid']);
        $recorder = new ImportFailureRecorder;
        $recorder->recordFailures([$failure], [], $task);

        $this->assertDatabaseHas('failed_rows', [
            'task_id' => $task->id,
            'row' => 4,
            'key' => 'unknown_field',
            'message' => 'Invalid',
        ]);
    }
}

<?php

namespace Tests\Integration;

use App\Models\FailedRow;
use App\Models\File;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_endpoint_returns_tasks_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        Sanctum::actingAs($user);

        $file = File::create([
            'path' => 'files/projects.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'projects.xlsx',
        ]);

        Task::create([
            'user_id' => $user->id,
            'file_id' => $file->id,
            'status' => Task::STATUS_PROCESS,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        $response = $this->getJson('/api/tasks');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                ['id', 'user', 'file', 'status', 'failed_rows_count'],
            ],
        ]);
    }

    public function test_failed_rows_endpoint_returns_rows_for_task(): void
    {
        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        Sanctum::actingAs($user);

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
        ]);

        FailedRow::create([
            'key' => 'Тип',
            'row' => 2,
            'message' => 'Required',
            'task_id' => $task->id,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}/failed-rows");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                ['id', 'key', 'row', 'message', 'task_id', 'created_at'],
            ],
        ]);
    }

    public function test_task_show_endpoint_returns_task(): void
    {
        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        Sanctum::actingAs($user);

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

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['id', 'user', 'file', 'status', 'status_code', 'failed_rows_count'],
        ]);
    }

    public function test_tasks_endpoint_is_scoped_to_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        Sanctum::actingAs($user);

        $file = File::create([
            'path' => 'files/projects.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'projects.xlsx',
        ]);

        Task::create([
            'user_id' => $otherUser->id,
            'file_id' => $file->id,
            'status' => Task::STATUS_PROCESS,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        $response = $this->getJson('/api/tasks');

        $response->assertOk();
        $response->assertJson([
            'data' => [],
        ]);
    }

    public function test_failed_rows_endpoint_denies_other_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        Sanctum::actingAs($user);

        $file = File::create([
            'path' => 'files/projects.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'projects.xlsx',
        ]);

        $task = Task::create([
            'user_id' => $otherUser->id,
            'file_id' => $file->id,
            'status' => Task::STATUS_ERROR,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}/failed-rows");

        $response->assertForbidden();
    }
}

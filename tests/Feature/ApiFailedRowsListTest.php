<?php

namespace Tests\Feature;

use App\Models\FailedRow;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiFailedRowsListTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_failed_rows_for_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        FailedRow::create([
            'task_id' => $task->id,
            'row' => 2,
            'row_number' => 2,
            'key' => 'Email',
            'message' => 'Invalid email',
            'errors' => ['email' => ['Invalid email']],
            'is_corrected' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/tasks/{$task->id}/failed-rows");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
    }
}

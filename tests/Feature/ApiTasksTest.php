<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_only_user_tasks(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Task::factory()->for($user)->count(2)->create();
        Task::factory()->for($other)->count(1)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }
}

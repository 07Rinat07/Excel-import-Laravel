<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskVisibilityScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_scope_limits_to_owner(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $fileA = File::create([
            'path' => 'files/a.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'a.xlsx',
        ]);
        $fileB = File::create([
            'path' => 'files/b.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'b.xlsx',
        ]);

        Task::create([
            'user_id' => $userA->id,
            'file_id' => $fileA->id,
            'status' => Task::STATUS_PROCESS,
            'type' => 1,
        ]);
        Task::create([
            'user_id' => $userB->id,
            'file_id' => $fileB->id,
            'status' => Task::STATUS_PROCESS,
            'type' => 1,
        ]);

        $this->assertCount(1, Task::visibleTo($userA)->get());
        $this->assertCount(1, Task::visibleTo($userB)->get());
    }
}

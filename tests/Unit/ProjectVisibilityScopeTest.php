<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectVisibilityScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_scope_limits_to_owner_tasks(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);

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

        $taskA = Task::create([
            'user_id' => $userA->id,
            'file_id' => $fileA->id,
            'status' => Task::STATUS_PROCESS,
            'type' => 1,
            'type_id' => $type->id,
        ]);
        $taskB = Task::create([
            'user_id' => $userB->id,
            'file_id' => $fileB->id,
            'status' => Task::STATUS_PROCESS,
            'type' => 1,
            'type_id' => $type->id,
        ]);

        Project::create([
            'type_id' => $type->id,
            'task_id' => $taskA->id,
            'title' => 'Project A',
            'created_at_time' => now()->toDateString(),
            'contracted_at' => now()->toDateString(),
        ]);
        Project::create([
            'type_id' => $type->id,
            'task_id' => $taskB->id,
            'title' => 'Project B',
            'created_at_time' => now()->toDateString(),
            'contracted_at' => now()->toDateString(),
        ]);

        $this->assertCount(1, Project::visibleTo($userA)->get());
        $this->assertCount(1, Project::visibleTo($userB)->get());
    }
}

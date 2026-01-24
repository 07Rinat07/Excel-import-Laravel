<?php

namespace Tests\Integration;

use App\Jobs\ImportProjectExcelFileJob;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectImportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_import_creates_task_and_dispatches_job(): void
    {
        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create(
            'projects.xlsx',
            10,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $response = $this->postJson('/api/projects/import', [
            'file' => $file,
            'type_id' => $type->id,
        ]);

        $response->assertStatus(202);
        $response->assertJsonStructure(['message', 'task_id']);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'type' => 1,
            'type_id' => $type->id,
            'status' => Task::STATUS_PROCESS,
        ]);

        Bus::assertDispatched(ImportProjectExcelFileJob::class);
    }

    public function test_api_import_accepts_csv(): void
    {
        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('projects.csv', 10, 'text/csv');

        $response = $this->postJson('/api/projects/import', [
            'file' => $file,
            'type_id' => $type->id,
        ]);

        $response->assertStatus(202);
        Bus::assertDispatched(ImportProjectExcelFileJob::class);
    }

    public function test_api_import_accepts_tsv(): void
    {
        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('projects.tsv', 10, 'text/tab-separated-values');

        $response = $this->postJson('/api/projects/import', [
            'file' => $file,
            'type_id' => $type->id,
        ]);

        $response->assertStatus(202);
        Bus::assertDispatched(ImportProjectExcelFileJob::class);
    }
}

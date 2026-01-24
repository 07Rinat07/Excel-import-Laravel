<?php

namespace Tests\Feature;

use App\Jobs\ImportProjectExcelFileJob;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectImportRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_request_dispatches_job_and_creates_task(): void
    {
        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        $file = UploadedFile::fake()->create(
            'projects.xlsx',
            10,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $response = $this->actingAs($user)->post(route('project.import.store'), [
            'file' => $file,
            'type_id' => $type->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('message');

        $this->assertDatabaseHas('files', [
            'title' => 'projects.xlsx',
        ]);
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'type' => 1,
            'type_id' => $type->id,
            'status' => Task::STATUS_PROCESS,
        ]);

        Bus::assertDispatched(ImportProjectExcelFileJob::class);
    }

    public function test_import_request_rejects_invalid_file_type(): void
    {
        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        $file = UploadedFile::fake()->create('projects.exe', 10, 'application/x-msdownload');

        $response = $this->actingAs($user)->post(route('project.import.store'), [
            'file' => $file,
            'type_id' => $type->id,
        ]);

        $response->assertSessionHasErrors(['file']);
        Bus::assertNotDispatched(ImportProjectExcelFileJob::class);
    }

    public function test_import_request_rejects_invalid_type(): void
    {
        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create(
            'projects.xlsx',
            10,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $response = $this->actingAs($user)->post(route('project.import.store'), [
            'file' => $file,
            'type_id' => 999,
        ]);

        $response->assertSessionHasErrors(['type_id']);
        Bus::assertNotDispatched(ImportProjectExcelFileJob::class);
    }

    public function test_import_request_accepts_csv(): void
    {
        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        $file = UploadedFile::fake()->create('projects.csv', 10, 'text/csv');

        $response = $this->actingAs($user)->post(route('project.import.store'), [
            'file' => $file,
            'type_id' => $type->id,
        ]);

        $response->assertRedirect();
        Bus::assertDispatched(ImportProjectExcelFileJob::class);
    }

    public function test_import_request_accepts_tsv(): void
    {
        Bus::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $type = Type::create(['title' => 'Type A']);
        $file = UploadedFile::fake()->create('projects.tsv', 10, 'text/tab-separated-values');

        $response = $this->actingAs($user)->post(route('project.import.store'), [
            'file' => $file,
            'type_id' => $type->id,
        ]);

        $response->assertRedirect();
        Bus::assertDispatched(ImportProjectExcelFileJob::class);
    }
}

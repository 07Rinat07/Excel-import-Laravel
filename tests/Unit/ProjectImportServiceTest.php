<?php

namespace Tests\Unit;

use App\Imports\UniversalProjectImport;
use App\Models\File;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use App\Services\Import\ImportFailureRecorder;
use App\Services\ProjectImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ProjectImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_imports_with_universal_import(): void
    {
        Excel::fake();

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

        $service = new ProjectImportService(new ImportFailureRecorder);
        $service->import($task, $file->path);

        Excel::assertImported($file->path, 'public', function ($import) {
            return $import instanceof UniversalProjectImport;
        });
    }
}

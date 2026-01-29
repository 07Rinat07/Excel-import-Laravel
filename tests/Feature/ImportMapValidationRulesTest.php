<?php

namespace Tests\Feature;

use App\Jobs\ImportProjectExcelFileJob;
use App\Models\ExcelTemplateColumn;
use App\Models\File;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportMapValidationRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_map_stores_validation_rules(): void
    {
        Queue::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $type = Type::factory()->create();
        $file = File::create([
            'path' => 'files/test.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'test.xlsx',
        ]);

        $task = Task::create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'type' => 1,
            'type_id' => $type->id,
            'status' => Task::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)->post(route('project.import.map.store', $task->id), [
            'columns' => [
                [
                    'index' => 0,
                    'include' => true,
                    'name' => 'Email',
                    'data_type' => 'string',
                    'required' => true,
                    'validation_rules' => 'required|email',
                ],
                [
                    'index' => 1,
                    'include' => true,
                    'name' => 'Name',
                    'data_type' => 'string',
                    'required' => false,
                    'validation_rules' => '',
                ],
            ],
            'sheet_index' => 0,
        ]);

        $response->assertRedirect(route('task.index'));

        $this->assertDatabaseHas('excel_template_columns', [
            'label' => 'Email',
        ]);

        $column = ExcelTemplateColumn::where('label', 'Email')->first();
        $this->assertIsArray($column->validation_rules);
        $this->assertContains('required', $column->validation_rules);
        $this->assertContains('email', $column->validation_rules);

        Queue::assertPushed(ImportProjectExcelFileJob::class);
    }
}

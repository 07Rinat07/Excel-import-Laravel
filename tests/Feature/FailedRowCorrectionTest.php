<?php

namespace Tests\Feature;

use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use App\Models\FailedRow;
use App\Models\File;
use App\Models\Task;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FailedRowCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_correct_failed_row_via_api(): void
    {
        $user = User::factory()->create();
        $type = Type::factory()->create();
        $file = File::create([
            'path' => 'files/test.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'test.xlsx',
        ]);

        $template = ExcelTemplate::create([
            'type_id' => $type->id,
            'name' => 'Test Template',
            'header_hash' => 'hash',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $emailColumn = ExcelTemplateColumn::create([
            'template_id' => $template->id,
            'key' => 'email',
            'label' => 'Email',
            'data_type' => 'string',
            'is_required' => true,
            'validation_rules' => ['required', 'email'],
            'position' => 0,
        ]);

        $task = Task::create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'type' => 1,
            'type_id' => $type->id,
            'template_id' => $template->id,
            'status' => Task::STATUS_ERROR,
        ]);

        $failedRow = FailedRow::create([
            'task_id' => $task->id,
            'row' => 2,
            'row_number' => 2,
            'key' => 'Email',
            'message' => 'Email must be a valid email',
            'data' => ['email' => 'bad'],
            'errors' => ['email' => ['Email must be a valid email']],
            'is_corrected' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson(route('api.failed_rows.update', $failedRow->id), [
            'corrected_data' => ['email' => 'valid@example.com'],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('failed_rows', [
            'id' => $failedRow->id,
            'is_corrected' => true,
        ]);
    }
}

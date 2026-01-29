<?php

namespace Tests\Feature;

use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use App\Models\Project;
use App\Models\ProjectValue;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EnhancedExportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_enhanced_exports_return_files(): void
    {
        if (! class_exists(\ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive extension is not available.');
        }

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $type = Type::factory()->create();
        $template = ExcelTemplate::factory()->create([
            'type_id' => $type->id,
            'created_by' => $user->id,
        ]);

        $colA = ExcelTemplateColumn::create([
            'template_id' => $template->id,
            'key' => 'email',
            'label' => 'Email',
            'data_type' => 'string',
            'is_required' => true,
            'validation_rules' => ['required', 'email'],
            'position' => 0,
        ]);
        $colB = ExcelTemplateColumn::create([
            'template_id' => $template->id,
            'key' => 'name',
            'label' => 'Name',
            'data_type' => 'string',
            'is_required' => false,
            'position' => 1,
        ]);

        $task = \App\Models\Task::factory()->for($user)->create([
            'type_id' => $type->id,
            'template_id' => $template->id,
        ]);

        $project = Project::factory()->create([
            'type_id' => $type->id,
            'template_id' => $template->id,
            'task_id' => $task->id,
        ]);

        ProjectValue::create([
            'project_id' => $project->id,
            'template_column_id' => $colA->id,
            'value' => 'test@example.com',
        ]);
        ProjectValue::create([
            'project_id' => $project->id,
            'template_column_id' => $colB->id,
            'value' => 'John',
        ]);

        $this->get("/api/projects/{$project->id}/export/formatted")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->get("/api/projects/{$project->id}/export/report")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->get("/api/projects/{$project->id}/export/validated")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}

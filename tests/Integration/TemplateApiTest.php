<?php

namespace Tests\Integration;

use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TemplateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_templates_endpoint_lists_templates_with_columns(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $type = Type::create(['title' => 'Type A']);
        $template = ExcelTemplate::create([
            'type_id' => $type->id,
            'name' => 'Template A',
            'is_active' => true,
        ]);

        ExcelTemplateColumn::create([
            'template_id' => $template->id,
            'key' => 'name',
            'label' => 'Name',
            'data_type' => 'string',
            'is_required' => true,
            'position' => 0,
        ]);

        $response = $this->getJson('/api/templates');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'type_id',
                    'is_active',
                    'columns' => [
                        ['id', 'key', 'label', 'data_type', 'is_required', 'position'],
                    ],
                ],
            ],
        ]);
    }
}

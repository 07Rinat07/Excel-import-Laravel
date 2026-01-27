<?php

namespace Tests\Unit;

use App\Exports\ProjectValuesMultiSheetQueryExport;
use App\Exports\ProjectValuesQueryExport;
use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use App\Models\Project;
use App\Models\ProjectValue;
use App\Models\Type;
use App\Services\Export\ProjectExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ProjectExportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_by_type_uses_query_export(): void
    {
        Excel::fake();

        $type = Type::create(['title' => 'Type A']);
        $template = ExcelTemplate::create([
            'type_id' => $type->id,
            'name' => 'Template A',
            'header_hash' => 'hash',
            'is_active' => true,
        ]);
        $column = ExcelTemplateColumn::create([
            'template_id' => $template->id,
            'key' => 'name',
            'label' => 'Name',
            'data_type' => 'string',
            'is_required' => false,
            'position' => 0,
        ]);
        $project = Project::create([
            'type_id' => $type->id,
            'template_id' => $template->id,
            'row_index' => 1,
            'sheet_name' => 'SheetA',
            'sheet_index' => 0,
            'title' => 'Project A',
            'created_at_time' => now()->toDateString(),
            'contracted_at' => now()->toDateString(),
        ]);
        ProjectValue::create([
            'project_id' => $project->id,
            'template_column_id' => $column->id,
            'value' => 'Value A',
        ]);

        $service = app(ProjectExportService::class);
        $service->exportByType($type, 'csv');

        Excel::assertDownloaded("type-{$type->id}.csv", function ($export) {
            return $export instanceof ProjectValuesQueryExport;
        });
    }

    public function test_export_by_type_uses_multi_sheet_query_export_for_multiple_sheets(): void
    {
        Excel::fake();

        $type = Type::create(['title' => 'Type B']);
        $template = ExcelTemplate::create([
            'type_id' => $type->id,
            'name' => 'Template B',
            'header_hash' => 'hash-b',
            'is_active' => true,
        ]);
        $column = ExcelTemplateColumn::create([
            'template_id' => $template->id,
            'key' => 'name',
            'label' => 'Name',
            'data_type' => 'string',
            'is_required' => false,
            'position' => 0,
        ]);

        $first = Project::create([
            'type_id' => $type->id,
            'template_id' => $template->id,
            'row_index' => 1,
            'sheet_name' => 'SheetOne',
            'sheet_index' => 0,
            'title' => 'Project One',
            'created_at_time' => now()->toDateString(),
            'contracted_at' => now()->toDateString(),
        ]);
        ProjectValue::create([
            'project_id' => $first->id,
            'template_column_id' => $column->id,
            'value' => 'Value One',
        ]);

        $second = Project::create([
            'type_id' => $type->id,
            'template_id' => $template->id,
            'row_index' => 2,
            'sheet_name' => 'SheetTwo',
            'sheet_index' => 1,
            'title' => 'Project Two',
            'created_at_time' => now()->toDateString(),
            'contracted_at' => now()->toDateString(),
        ]);
        ProjectValue::create([
            'project_id' => $second->id,
            'template_column_id' => $column->id,
            'value' => 'Value Two',
        ]);

        $service = app(ProjectExportService::class);
        $service->exportByType($type, 'xlsx');

        Excel::assertDownloaded("type-{$type->id}.xlsx", function ($export) {
            return $export instanceof ProjectValuesMultiSheetQueryExport;
        });
    }
}

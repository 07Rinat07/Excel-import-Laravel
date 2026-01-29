<?php

namespace Tests\Feature;

use App\Models\ExcelTemplate;
use App\Models\ExcelTemplateColumn;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class SheetSelectionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sheet_endpoints_work(): void
    {
        if (! class_exists(\ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive extension is not available.');
        }

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $path = storage_path('app/public/files');
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $filePath = $path.'/sheet-test.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('First');
        $sheet->setCellValue('A1', 'Email');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('A2', 'test@example.com');
        $sheet->setCellValue('B2', 'John');
        $spreadsheet->createSheet()->setTitle('Second');
        (new Xlsx($spreadsheet))->save($filePath);

        $file = File::create([
            'path' => 'files/sheet-test.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'title' => 'sheet-test.xlsx',
        ]);

        $template = ExcelTemplate::factory()->create(['created_by' => $user->id]);
        ExcelTemplateColumn::create([
            'template_id' => $template->id,
            'key' => 'email',
            'label' => 'Email',
            'data_type' => 'string',
            'is_required' => true,
            'position' => 0,
        ]);

        $this->getJson("/api/files/{$file->id}/sheets")
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->postJson("/api/files/{$file->id}/sheets/headers", ['sheet_index' => 0])
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.headers.0', 'Email');

        $this->postJson("/api/files/{$file->id}/sheets/preview", ['sheet_index' => 0, 'rows_count' => 1])
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->postJson("/api/files/{$file->id}/sheets/select", [
            'sheet_index' => 0,
            'template_id' => $template->id,
            'task_name' => 'Import Sheet',
        ])->assertStatus(200)->assertJsonPath('success', true);
    }
}

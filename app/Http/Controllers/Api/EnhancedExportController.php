<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectValue;
use App\Services\Export\EnhancedExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EnhancedExportController extends Controller
{
    public function __construct(
        private EnhancedExportService $exportService
    ) {}

    /**
     * Export project data with formatting
     */
    public function exportFormattedData(Request $request, Project $project): Response
    {
        $this->authorize('view', $project);

        $projectName = $project->title ?? $project->name ?? 'project';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Project Data');

        // Get template columns
        $templateColumns = $project->template->columns()
            ->orderBy('position')
            ->get();

        // Add headers
        $col = 'A';
        foreach ($templateColumns as $column) {
            $sheet->getCell($col . '1')->setValue($column->label);
            $col++;
        }

        // Style header
        $this->exportService->styleHeader($spreadsheet);

        // Get project values
        $values = ProjectValue::where('project_id', $project->id)
            ->orderBy('id')
            ->get()
            ->keyBy('template_column_id');

        // Add data
        $row = 2;
        $colIndex = 'A';
        foreach ($templateColumns as $column) {
            $value = $values->get($column->id);
            $sheet->getCell($colIndex . $row)->setValue($value?->value);
            $colIndex++;
        }

        // Apply formatting
        $this->exportService->autoFitColumns($spreadsheet);
        $this->exportService->alternateRowColors($spreadsheet);
        $this->exportService->freezeHeaderRow($spreadsheet);

        // Add summary statistics
        $stats = [
            'Total Records' => count($values),
            'Columns' => count($templateColumns),
            'Export Date' => date('Y-m-d H:i:s'),
            'Project' => $projectName,
        ];
        $this->exportService->addSummarySheet($spreadsheet, $stats);
        $summarySheet = $spreadsheet->getSheetByName('Summary');
        if ($summarySheet) {
            $spreadsheet->setActiveSheetIndexByName('Summary');
            $startRow = 3;
            $endRow = $startRow + count($stats) - 1;
            $this->exportService->addBarChart(
                $spreadsheet,
                "Summary!A{$startRow}:A{$endRow}",
                "Summary!B{$startRow}:B{$endRow}",
                'Summary'
            );
            $spreadsheet->setActiveSheetIndexByName('Project Data');
        }

        // Generate file
        return $this->downloadSpreadsheet($spreadsheet, $projectName . '_formatted.xlsx');
    }

    /**
     * Export with custom report layout
     */
    public function exportReport(Request $request, Project $project): Response
    {
        $this->authorize('view', $project);

        $projectName = $project->title ?? $project->name ?? 'project';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report');

        // Title
        $sheet->getCell('A1')->setValue('PROJECT REPORT');
        $sheet->getCell('A1')->getStyle()->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A1:E1');

        // Project info
        $row = 3;
        $sheet->getCell('A' . $row)->setValue('Project Name:');
        $sheet->getCell('B' . $row)->setValue($projectName);
        $row++;
        $sheet->getCell('A' . $row)->setValue('Export Date:');
        $sheet->getCell('B' . $row)->setValue(Date::PHPToExcel(now()));
        $row += 2;

        // Data table
        $templateColumns = $project->template->columns()->orderBy('position')->get();
        $col = 'A';
        foreach ($templateColumns as $column) {
            $sheet->getCell($col . $row)->setValue($column->label);
            $col++;
        }

        $this->exportService->styleHeader($spreadsheet, $row);

        // Add data
        $values = ProjectValue::where('project_id', $project->id)
            ->get()
            ->keyBy('template_column_id');

        $row++;
        $colIndex = 'A';
        foreach ($templateColumns as $column) {
            $value = $values->get($column->id);
            $sheet->getCell($colIndex . $row)->setValue($value?->value);
            $colIndex++;
        }

        // Apply formatting
        $this->exportService->autoFitColumns($spreadsheet);
        $this->exportService->alternateRowColors($spreadsheet, 4);
        $this->exportService->addImage(
            $spreadsheet,
            public_path('images/project_image.png'),
            'F1'
        );

        return $this->downloadSpreadsheet($spreadsheet, $projectName . '_report.xlsx');
    }

    /**
     * Export with data validation
     */
    public function exportWithValidation(Request $request, Project $project): Response
    {
        $this->authorize('view', $project);

        $projectName = $project->title ?? $project->name ?? 'project';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $templateColumns = $project->template->columns()->orderBy('position')->get();

        // Add headers
        $col = 'A';
        foreach ($templateColumns as $column) {
            $sheet->getCell($col . '1')->setValue($column->label);
            $col++;
        }

        $this->exportService->styleHeader($spreadsheet);

        // Add data
        $values = ProjectValue::where('project_id', $project->id)->get()->keyBy('template_column_id');
        $row = 2;
        $colIndex = 'A';
        foreach ($templateColumns as $column) {
            $value = $values->get($column->id);
            $sheet->getCell($colIndex . $row)->setValue($value?->value);
            $colIndex++;
        }

        // Add data validation based on template rules
        foreach ($templateColumns as $colIndex => $column) {
            if ($column->validation_rules) {
                $columnLetter = chr(65 + $colIndex); // A, B, C...
                $range = $columnLetter . '2:' . $columnLetter . ($row - 1);

                foreach ($column->validation_rules as $rule) {
                    if ($rule === 'required') {
                        $validation = $sheet->getDataValidation($range);
                        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE);
                        $validation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_NOTEQUAL);
                    }
                }
            }
        }

        $this->exportService->autoFitColumns($spreadsheet);
        $this->exportService->freezeHeaderRow($spreadsheet);

        return $this->downloadSpreadsheet($spreadsheet, $projectName . '_validated.xlsx');
    }

    /**
     * Download spreadsheet as response
     */
    private function downloadSpreadsheet(Spreadsheet $spreadsheet, string $filename): Response
    {
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 0,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Excel;

use App\Domain\Export\Services\ExcelExportService;
use App\Domain\Export\ValueObjects\ExportFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

/**
 * Service: PhpSpreadsheetExportService
 *
 * Implementation of ExcelExportService using PhpSpreadsheet library.
 * Handles exporting data to XLSX, CSV, and TSV formats.
 */
final class PhpSpreadsheetExportService implements ExcelExportService
{
    /**
     * Export data to Excel file.
     *
     * @param array<string> $headers Column headers
     * @param array<array<mixed>> $rows Data rows to export
     * @param ExportFormat $format Export format
     * @return string File path of exported file
     */
    public function export(
        array $headers,
        array $rows,
        ExportFormat $format,
    ): string {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write headers
        $columnIndex = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $header);
            $columnIndex++;
        }

        // Write data rows
        $rowIndex = 2;
        foreach ($rows as $row) {
            $columnIndex = 1;
            foreach ($row as $value) {
                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $value);
                $columnIndex++;
            }
            $rowIndex++;
        }

        // Generate file path
        $fileName = 'export_' . time() . '.' . $format->value();
        $filePath = storage_path('exports/' . $fileName);

        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Save file based on format
        match ($format->value()) {
            'xlsx' => $this->saveAsXlsx($spreadsheet, $filePath),
            'csv' => $this->saveCsv($spreadsheet, $filePath),
            'tsv' => $this->saveTsv($spreadsheet, $filePath),
            default => throw new Exception("Unsupported export format: {$format->value()}"),
        };

        return $filePath;
    }

    /**
     * Get supported export formats.
     *
     * @return array<string>
     */
    public function supportedFormats(): array
    {
        return ['xlsx', 'csv', 'tsv'];
    }

    /**
     * Save spreadsheet as XLSX file.
     */
    private function saveAsXlsx(Spreadsheet $spreadsheet, string $filePath): void
    {
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    /**
     * Save spreadsheet as CSV file.
     */
    private function saveCsv(Spreadsheet $spreadsheet, string $filePath): void
    {
        $writer = new Csv($spreadsheet);
        $writer->save($filePath);
    }

    /**
     * Save spreadsheet as TSV file (CSV with tab delimiter).
     */
    private function saveTsv(Spreadsheet $spreadsheet, string $filePath): void
    {
        $writer = new Csv($spreadsheet);
        $writer->setDelimiter("\t");
        $writer->save($filePath);
    }
}

<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class SheetProcessingService
{
    /**
     * Get all available sheets from Excel file
     */
    public function getAvailableSheets(string $filePath): array
    {
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $info = $reader->listWorksheetInfo($filePath);

            $sheets = [];
            foreach ($info as $index => $sheet) {
                $sheets[] = [
                    'index' => $index,
                    'name' => $sheet['worksheetName'] ?? ('Sheet '.($index + 1)),
                    'total_rows' => $sheet['totalRows'] ?? null,
                    'total_columns' => $sheet['totalColumns'] ?? null,
                    'has_data' => isset($sheet['totalRows']) ? ((int) $sheet['totalRows'] > 1) : null,
                ];
            }

            return $sheets;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get headers from a specific sheet
     */
    public function getSheetHeaders(string $filePath, int $sheetIndex = 0): array
    {
        try {
            $sheetName = $this->resolveSheetName($filePath, $sheetIndex);
            $sheet = $this->loadSheetRows($filePath, $sheetName, 1, 10);

            [$headerRow, $highestColumnIndex] = $this->detectHeaderRow($sheet);
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $headers[] = $sheet->getCellByColumnAndRow($col, $headerRow)->getValue();
            }

            return $headers;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Read all rows from specific sheet
     */
    public function readSheetData(
        string $filePath,
        int $sheetIndex = 0,
        bool $skipHeader = true,
        ?int $maxRows = null
    ): array
    {
        try {
            $sheetName = $this->resolveSheetName($filePath, $sheetIndex);
            $headerSheet = $this->loadSheetRows($filePath, $sheetName, 1, 10);
            [$headerRow, $highestColumnIndex] = $this->detectHeaderRow($headerSheet);
            $previewCount = $maxRows ?? 50;
            $startRow = $skipHeader ? ($headerRow + 1) : 1;
            $endRow = $startRow + $previewCount - 1;
            $sheet = $this->loadSheetRows($filePath, $sheetName, $startRow, $endRow);

            $data = [];
            $rowCount = 0;

            foreach ($sheet->getRowIterator($startRow) as $row) {
                if ($previewCount && $rowCount >= $previewCount) {
                    break;
                }

                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $rowData[] = $sheet->getCellByColumnAndRow($col, $row->getRowIndex())->getValue();
                }

                if (!empty(array_filter($rowData))) { // Skip completely empty rows
                    $data[] = $rowData;
                    $rowCount++;
                }
            }

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get sheet statistics
     */
    public function getSheetStatistics(string $filePath, int $sheetIndex = 0): array
    {
        try {
            $sheetName = $this->resolveSheetName($filePath, $sheetIndex);
            $sheet = $this->loadSheetRows($filePath, $sheetName, 1, 1);

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            return [
                'name' => $sheet->getTitle(),
                'total_rows' => $highestRow,
                'total_columns' => Coordinate::columnIndexFromString($highestColumn),
                'has_data' => $highestRow > 1,
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function detectHeaderRow(Worksheet $sheet, int $maxScanRows = 10): array
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());
        $limit = min($highestRow, $maxScanRows);

        for ($row = 1; $row <= $limit; $row++) {
            $hasData = false;
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $value = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                if ($value !== null && $value !== '') {
                    $hasData = true;
                    break;
                }
            }
            if ($hasData) {
                return [$row, $highestColumnIndex];
            }
        }

        return [1, $highestColumnIndex];
    }

    /**
     * Validate that sheet index exists and has data
     */
    public function validateSheetIndex(string $filePath, int $sheetIndex): bool
    {
        try {
            $sheetName = $this->resolveSheetName($filePath, $sheetIndex);
            if ($sheetName === null) {
                return false;
            }
            $sheet = $this->loadSheetRows($filePath, $sheetName, 1, 1);
            return $sheet->getHighestRow() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Compare sheets to find the most suitable one for import
     * (e.g., with most data)
     */
    public function findBestSheet(string $filePath): int
    {
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $info = $reader->listWorksheetInfo($filePath);
            $bestIndex = 0;
            $maxRows = 0;

            foreach ($info as $index => $sheet) {
                $rowCount = (int) ($sheet['totalRows'] ?? 0);
                if ($rowCount > $maxRows) {
                    $maxRows = $rowCount;
                    $bestIndex = $index;
                }
            }

            return $bestIndex;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function resolveSheetName(string $filePath, int $sheetIndex): ?string
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $info = $reader->listWorksheetInfo($filePath);
        if (! isset($info[$sheetIndex])) {
            return null;
        }

        return $info[$sheetIndex]['worksheetName'] ?? null;
    }

    private function loadSheetRows(string $filePath, ?string $sheetName, int $startRow, int $endRow): Worksheet
    {
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        if ($sheetName) {
            $reader->setLoadSheetsOnly([$sheetName]);
        }
        $reader->setReadFilter(new class($startRow, $endRow) implements IReadFilter {
            public function __construct(private int $startRow, private int $endRow) {}

            public function readCell($column, $row, $worksheetName = ''): bool
            {
                return $row >= $this->startRow && $row <= $this->endRow;
            }
        });

        $spreadsheet = $reader->load($filePath);

        return $spreadsheet->getActiveSheet();
    }
}

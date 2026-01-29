<?php

declare(strict_types=1);

namespace App\Infrastructure\Excel;

use App\Domain\Import\Services\ExcelParserService;
use App\Domain\Import\ValueObjects\FileContent;
use App\Domain\Import\ValueObjects\FileName;
use App\Domain\Import\ValueObjects\Row;
use App\Domain\Import\Exceptions\InvalidFileException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use Throwable;

/**
 * Service: PhpSpreadsheetParserService
 *
 * Implementation of ExcelParserService using PhpSpreadsheet library.
 * Supports XLSX, XLS, CSV, ODS formats.
 */
final class PhpSpreadsheetParserService implements ExcelParserService
{
    /**
     * Parse Excel file and extract headers and data rows.
     *
     * @return array{headers: array<string>, rows: array<Row>}
     * @throws InvalidFileException
     */
    public function parse(FileName $fileName, FileContent $content): array
    {
        try {
            // Load the spreadsheet
            $spreadsheet = IOFactory::load($content->path());
            $worksheet = $spreadsheet->getActiveSheet();

            $headers = [];
            $rows = [];

            $rowIterator = $worksheet->getRowIterator();
            $rowIterator->setIterateOnlyExistingCells(false);

            $isFirstRow = true;

            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];

                foreach ($cellIterator as $cell) {
                    $value = $cell->getValue();

                    // Handle different value types
                    if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                        $value = $value->getPlainText();
                    }

                    // Trim whitespace
                    if (is_string($value)) {
                        $value = trim($value);
                    }

                    $rowData[] = $value ?? '';
                }

                // Skip completely empty rows
                if ($this->isEmptyRow($rowData)) {
                    continue;
                }

                if ($isFirstRow) {
                    // First row is headers
                    $headers = array_map(fn($val) => (string) $val, $rowData);
                    $isFirstRow = false;
                } else {
                    // Map row data to associative array using headers
                    $mappedRow = [];
                    foreach ($headers as $index => $header) {
                        $mappedRow[$header] = $rowData[$index] ?? null;
                    }

                    $rows[] = new Row($mappedRow);
                }
            }

            if (empty($headers)) {
                throw new InvalidFileException('File has no headers');
            }

            return [
                'headers' => $headers,
                'rows' => $rows,
            ];
        } catch (SpreadsheetException $e) {
            throw new InvalidFileException('Failed to parse Excel file: ' . $e->getMessage());
        } catch (Throwable $e) {
            throw new InvalidFileException('Failed to parse file: ' . $e->getMessage());
        }
    }

    /**
     * Get supported file extensions.
     *
     * @return array<string>
     */
    public function supportedExtensions(): array
    {
        return ['xlsx', 'xls', 'csv', 'ods', 'tsv'];
    }

    /**
     * Check if a row contains only empty values.
     *
     * @param array<mixed> $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && $value !== '' && trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
    }
}

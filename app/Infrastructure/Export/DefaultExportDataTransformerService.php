<?php

declare(strict_types=1);

namespace App\Infrastructure\Export;

use App\Domain\Export\Services\ExportDataTransformerService;
use App\Domain\Export\ValueObjects\ExportColumns;

/**
 * Service: DefaultExportDataTransformerService
 *
 * Default implementation of ExportDataTransformerService.
 * Transforms and normalizes data for export operations.
 */
final class DefaultExportDataTransformerService implements ExportDataTransformerService
{
    /**
     * Transform source data according to selected columns.
     *
     * @param array<array<mixed>> $sourceData Raw data from database
     * @param ExportColumns $selectedColumns Selected columns to include
     * @return array{headers: array<string>, rows: array<array<mixed>>}
     */
    public function transform(
        array $sourceData,
        ExportColumns $selectedColumns,
    ): array {
        if (empty($sourceData)) {
            return ['headers' => [], 'rows' => []];
        }

        // Get headers from first row and filter by selected columns
        $firstRow = reset($sourceData);
        $allHeaders = array_keys((array) $firstRow);
        $headers = array_filter(
            $allHeaders,
            fn($header) => in_array(array_search($header, $allHeaders), $selectedColumns->toArray(), true)
        );

        // Transform rows
        $rows = [];
        foreach ($sourceData as $row) {
            $transformedRow = [];
            foreach ($headers as $header) {
                $transformedRow[$header] = $row[$header] ?? null;
            }
            $rows[] = array_values($transformedRow);
        }

        return [
            'headers' => array_values($headers),
            'rows' => $rows,
        ];
    }

    /**
     * Filter and normalize data rows.
     *
     * @param array<array<mixed>> $rows
     * @return array<array<mixed>>
     */
    public function normalizeRows(array $rows): array
    {
        return array_map(function ($row) {
            return array_map(function ($value) {
                // Convert null to empty string
                if ($value === null) {
                    return '';
                }

                // Convert booleans to string
                if (is_bool($value)) {
                    return $value ? 'Yes' : 'No';
                }

                // Convert dates to ISO format
                if ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
                    return $value->format('Y-m-d H:i:s');
                }

                // Convert arrays/objects to JSON
                if (is_array($value) || is_object($value)) {
                    return json_encode($value, JSON_UNESCAPED_UNICODE);
                }

                return (string) $value;
            }, $row);
        }, $rows);
    }
}

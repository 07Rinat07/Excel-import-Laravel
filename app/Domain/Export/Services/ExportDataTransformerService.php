<?php

declare(strict_types=1);

namespace App\Domain\Export\Services;

use App\Domain\Export\ValueObjects\ExportColumns;

/**
 * Service Interface: ExportDataTransformerService
 *
 * Responsible for transforming and formatting data before export.
 * Handles column selection, filtering, and data transformation.
 */
interface ExportDataTransformerService
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
    ): array;

    /**
     * Filter and normalize data rows.
     *
     * @param array<array<mixed>> $rows
     * @return array<array<mixed>>
     */
    public function normalizeRows(array $rows): array;
}

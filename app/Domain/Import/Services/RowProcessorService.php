<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Import\ValueObjects\Row;
use App\Domain\Import\ValueObjects\ColumnMapping;

/**
 * Service Interface: RowProcessorService
 *
 * Responsible for transforming and processing rows before storage.
 */
interface RowProcessorService
{
    /**
     * Process a single row with column mappings.
     *
     * @param array<ColumnMapping> $mappings
     * @return array<string, mixed>
     */
    public function process(Row $row, array $mappings): array;

    /**
     * Process multiple rows in batch.
     *
     * @param array<Row> $rows
     * @param array<ColumnMapping> $mappings
     * @return array<array<string, mixed>>
     */
    public function processBatch(array $rows, array $mappings): array;
}

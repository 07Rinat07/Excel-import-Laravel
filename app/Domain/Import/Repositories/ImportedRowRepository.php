<?php

declare(strict_types=1);

namespace App\Domain\Import\Repositories;

use App\Domain\Import\Entities\ImportedRow;

/**
 * Repository Interface: ImportedRowRepository
 *
 * Responsible for persisting and retrieving imported rows.
 */
interface ImportedRowRepository
{
    /**
     * Save imported row.
     */
    public function save(ImportedRow $row): void;

    /**
     * Save multiple rows in batch.
     *
     * @param array<ImportedRow> $rows
     */
    public function saveBatch(array $rows): void;

    /**
     * Find all rows for an import task.
     *
     * @return array<ImportedRow>
     */
    public function findByImportTaskId(int $importTaskId): array;

    /**
     * Find rows with validation errors for an import task.
     *
     * @return array<ImportedRow>
     */
    public function findInvalidByImportTaskId(int $importTaskId): array;

    /**
     * Delete all rows for an import task.
     */
    public function deleteByImportTaskId(int $importTaskId): void;
}

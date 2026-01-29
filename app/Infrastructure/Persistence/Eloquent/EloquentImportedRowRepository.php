<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Import\Entities\ImportedRow;
use App\Domain\Import\Repositories\ImportedRowRepository;
use App\Infrastructure\Persistence\Mappers\ImportedRowMapper;
use App\Models\FailedRow;

/**
 * Repository: EloquentImportedRowRepository
 *
 * Eloquent implementation of ImportedRowRepository.
 * Uses FailedRow table for persistence.
 */
final class EloquentImportedRowRepository implements ImportedRowRepository
{
    public function save(ImportedRow $row): void
    {
        $model = ImportedRowMapper::toPersistence($row);
        $model->save();
    }

    /**
     * @param array<ImportedRow> $rows
     */
    public function saveBatch(array $rows): void
    {
        // Use chunking for large batches
        foreach (array_chunk($rows, 1000) as $chunk) {
            foreach ($chunk as $row) {
                $this->save($row);
            }
        }
    }

    /**
     * @return array<ImportedRow>
     */
    public function findByImportTaskId(int $importTaskId): array
    {
        $models = FailedRow::where('task_id', $importTaskId)
            ->orderBy('row_number')
            ->get();

        return $models->map(fn(FailedRow $model) => ImportedRowMapper::toDomain($model))->toArray();
    }

    /**
     * @return array<ImportedRow>
     */
    public function findInvalidByImportTaskId(int $importTaskId): array
    {
        $models = FailedRow::where('task_id', $importTaskId)
            ->where('is_valid', false)
            ->orderBy('row_number')
            ->get();

        return $models->map(fn(FailedRow $model) => ImportedRowMapper::toDomain($model))->toArray();
    }

    public function deleteByImportTaskId(int $importTaskId): void
    {
        FailedRow::where('task_id', $importTaskId)->delete();
    }
}

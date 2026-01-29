<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Import\Entities\ImportedRow;
use App\Domain\Import\ValueObjects\RowNumber;
use App\Domain\Import\ValueObjects\Row;
use App\Domain\Import\ValueObjects\ValidationResult;
use App\Models\FailedRow as ImportedRowModel;
use DateTimeImmutable;

/**
 * Mapper: ImportedRowMapper
 *
 * Maps between domain ImportedRow entity and Eloquent FailedRow model.
 * Uses FailedRow table to store validation results.
 */
final class ImportedRowMapper
{
    /**
     * Convert domain entity to Eloquent model for persistence.
     */
    public static function toPersistence(ImportedRow $entity): ImportedRowModel
    {
        $model = new ImportedRowModel();
        $model->task_id = $entity->importTaskId;
        $model->row_number = $entity->rowNumber->value();
        $model->data = json_encode($entity->data->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $model->is_valid = $entity->isValid();
        if (!$entity->isValid()) {
            $model->error_messages = json_encode($entity->getErrors(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $model->created_at = $entity->createdAt;

        return $model;
    }

    /**
     * Convert Eloquent model to domain entity.
     */
    public static function toDomain(ImportedRowModel $model): ImportedRow
    {
        $data = json_decode($model->data ?? '[]', true) ?? [];
        $row = new Row($data);

        $errors = [];
        if ($model->error_messages) {
            $errors = json_decode($model->error_messages, true) ?? [];
        }

        $validationResult = $errors ? ValidationResult::failed($errors) : ValidationResult::success();

        return ImportedRow::create(
            importTaskId: $model->task_id,
            rowNumber: RowNumber::from($model->row_number),
            data: $row,
            validationResult: $validationResult,
        );
    }
}

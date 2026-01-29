<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Import\Entities\ImportTask;
use App\Domain\Import\ValueObjects\ImportTaskId;
use App\Domain\Import\ValueObjects\FileName;
use App\Domain\Import\ValueObjects\UserId;
use App\Domain\Import\ValueObjects\TemplateId;
use App\Domain\Import\ValueObjects\ImportStatus;
use App\Domain\Import\ValueObjects\MappingStrategy;
use App\Models\ExportLog as ImportTaskModel;
use DateTimeImmutable;

/**
 * Mapper: ImportTaskMapper
 *
 * Maps between domain ImportTask entity and Eloquent ExportLog model.
 * Uses ExportLog table to store import task state.
 */
final class ImportTaskMapper
{
    /**
     * Convert domain entity to Eloquent model for persistence.
     */
    public static function toPersistence(ImportTask $entity): ImportTaskModel
    {
        $model = new ImportTaskModel();
        // Let database auto-generate the ID if creating a new record
        if ($entity->id->value() > 0) {
            $model->id = $entity->id->value();
        }
        $model->file_name = $entity->fileName->value();
        $model->user_id = $entity->userId->value();
        $model->source_type = 'import';
        $model->source_id = $entity->templateId->value();
        $model->format = $entity->mappingStrategy->toString(); // Use format column for strategy
        $model->status = $entity->status->toString();
        $model->rows_processed = $entity->rowsProcessed;
        $model->failure_reason = $entity->failureReason;
        $model->created_at = $entity->createdAt;
        if ($entity->completedAt !== null) {
            $model->updated_at = $entity->completedAt;
        }

        return $model;
    }

    /**
     * Convert Eloquent model to domain entity.
     */
    public static function toDomain(ImportTaskModel $model): ImportTask
    {
        $completedAt = null;
        if ($model->updated_at && $model->updated_at !== $model->created_at) {
            $completedAt = $model->updated_at->toDateTimeImmutable();
        }

        return ImportTask::restore(
            id: ImportTaskId::from($model->id),
            fileName: new FileName($model->file_name),
            userId: UserId::from($model->user_id),
            templateId: TemplateId::from($model->source_id ?? 1),
            status: ImportStatus::from($model->status),
            mappingStrategy: MappingStrategy::from($model->format ?? 'manual'),
            createdAt: $model->created_at->toDateTimeImmutable(),
            completedAt: $completedAt,
            failureReason: $model->failure_reason,
            rowsProcessed: $model->rows_processed ?? 0,
        );
    }
}

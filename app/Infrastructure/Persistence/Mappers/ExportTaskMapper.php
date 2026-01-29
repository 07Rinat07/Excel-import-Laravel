<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mappers;

use App\Domain\Export\Entities\ExportTask;
use App\Domain\Export\ValueObjects\ExportTaskId;
use App\Domain\Export\ValueObjects\ExportFormat;
use App\Domain\Export\ValueObjects\ExportColumns;
use App\Domain\Export\ValueObjects\ExportStatus;
use App\Domain\Import\ValueObjects\UserId;
use App\Domain\Import\ValueObjects\TemplateId;
use App\Models\ExportLog as ExportTaskModel;
use DateTimeImmutable;

/**
 * Mapper: ExportTaskMapper
 *
 * Maps between domain ExportTask entity and Eloquent ExportLog model.
 */
final class ExportTaskMapper
{
    /**
     * Convert domain entity to Eloquent model for persistence.
     */
    public static function toPersistence(ExportTask $entity): ExportTaskModel
    {
        $model = new ExportTaskModel();
        $model->task_id = $entity->id->value(); // UUID as separate field
        $model->user_id = $entity->userId->value();
        $model->source_type = 'export';
        $model->source_id = $entity->templateId->value();
        $model->format = $entity->format->extension();
        $model->status = $entity->status->toString();
        $model->file_name = $entity->filePath;
        $model->failure_reason = $entity->failureReason;
        $model->columns_count = $entity->columns->count();
        $model->created_at = $entity->createdAt;
        if ($entity->completedAt !== null) {
            $model->updated_at = $entity->completedAt;
        }

        return $model;
    }

    /**
     * Convert Eloquent model to domain entity.
     */
    public static function toDomain(ExportTaskModel $model): ExportTask
    {
        $completedAt = null;
        if ($model->updated_at && $model->updated_at !== $model->created_at) {
            $completedAt = $model->updated_at->toDateTimeImmutable();
        }

        // Default to columns [1,2,3] if not specified
        $columnIds = [1, 2, 3];

        return ExportTask::restore(
            id: ExportTaskId::from($model->task_id), // Use task_id UUID
            format: ExportFormat::from($model->format),
            columns: ExportColumns::from($columnIds),
            userId: UserId::from($model->user_id),
            templateId: TemplateId::from($model->source_id ?? 1),
            status: ExportStatus::from($model->status),
            createdAt: $model->created_at->toDateTimeImmutable(),
            completedAt: $completedAt,
            filePath: $model->file_name,
            failureReason: $model->failure_reason,
        );
    }
}

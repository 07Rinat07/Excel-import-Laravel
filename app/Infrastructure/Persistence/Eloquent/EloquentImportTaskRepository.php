<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Import\Entities\ImportTask;
use App\Domain\Import\Repositories\ImportTaskRepository;
use App\Domain\Import\ValueObjects\ImportTaskId;
use App\Infrastructure\Persistence\Mappers\ImportTaskMapper;
use App\Models\ExportLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Repository: EloquentImportTaskRepository
 *
 * Eloquent implementation of ImportTaskRepository.
 * Uses ExportLog table for persistence.
 */
final class EloquentImportTaskRepository implements ImportTaskRepository
{
    public function save(ImportTask $task): void
    {
        $model = ImportTaskMapper::toPersistence($task);

        // Check if the record already exists in the database
        $existing = ExportLog::where('id', $task->id->value())
            ->where('source_type', 'import')
            ->first();

        if ($existing) {
            // Update existing record
            $model = $existing;
            $model->file_name = $model->file_name; // Keep original
            $model->user_id = $model->user_id; // Keep original
            $model->source_type = 'import';
            $model->source_id = $task->templateId->value();
            $model->format = $task->mappingStrategy->toString();
            $model->status = $task->status->toString();
            $model->rows_processed = $task->rowsProcessed;
            $model->failure_reason = $task->failureReason;
            if ($task->completedAt !== null) {
                $model->updated_at = $task->completedAt;
            }
        }

        $model->save();
    }

    public function findById(ImportTaskId $id): ?ImportTask
    {
        try {
            $model = ExportLog::findOrFail($id->value());
            return ImportTaskMapper::toDomain($model);
        } catch (ModelNotFoundException) {
            return null;
        }
    }

    /**
     * @return array<ImportTask>
     */
    public function findByUserId(int $userId): array
    {
        $models = ExportLog::where('user_id', $userId)
            ->where('source_type', 'import')
            ->orderBy('id', 'asc')
            ->get();

        return $models->map(fn(ExportLog $model) => ImportTaskMapper::toDomain($model))->toArray();
    }

    public function delete(ImportTaskId $id): void
    {
        ExportLog::destroy($id->value());
    }
}

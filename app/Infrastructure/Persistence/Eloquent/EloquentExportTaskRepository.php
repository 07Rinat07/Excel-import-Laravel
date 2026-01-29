<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Export\Entities\ExportTask;
use App\Domain\Export\Repositories\ExportTaskRepository;
use App\Domain\Export\ValueObjects\ExportTaskId;
use App\Infrastructure\Persistence\Mappers\ExportTaskMapper;
use App\Models\ExportLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Repository: EloquentExportTaskRepository
 *
 * Eloquent implementation of ExportTaskRepository.
 * Uses ExportLog table for persistence.
 */
final class EloquentExportTaskRepository implements ExportTaskRepository
{
    public function save(ExportTask $task): void
    {
        $taskId = $task->id->value();

        // Check if record exists by task_id UUID
        $existing = ExportLog::where('task_id', $taskId)->first();

        if ($existing) {
            // Update existing record
            $existing->status = $task->status->toString();
            $existing->file_name = $task->filePath;
            $existing->failure_reason = $task->failureReason;
            if ($task->completedAt !== null) {
                $existing->updated_at = $task->completedAt;
            }
            $existing->save();
        } else {
            // Create new record
            $model = ExportTaskMapper::toPersistence($task);
            $model->save();
        }
    }

    public function findById(ExportTaskId $id): ?ExportTask
    {
        try {
            $model = ExportLog::where('task_id', $id->value())->firstOrFail();
            return ExportTaskMapper::toDomain($model);
        } catch (ModelNotFoundException) {
            return null;
        }
    }

    /**
     * @return array<ExportTask>
     */
    public function findByUserId(int $userId): array
    {
        $models = ExportLog::where('user_id', $userId)
            ->where('source_type', 'export')
            ->orderByDesc('created_at')
            ->get();

        return $models->map(fn(ExportLog $model) => ExportTaskMapper::toDomain($model))->toArray();
    }

    public function delete(ExportTaskId $id): void
    {
        ExportLog::where('task_id', $id->value())->delete();
    }
}

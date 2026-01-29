<?php

declare(strict_types=1);

namespace App\Domain\Export\Repositories;

use App\Domain\Export\Entities\ExportTask;
use App\Domain\Export\ValueObjects\ExportTaskId;

/**
 * Repository Interface: ExportTaskRepository
 *
 * Responsible for persisting and retrieving ExportTask aggregates.
 */
interface ExportTaskRepository
{
    /**
     * Save export task.
     */
    public function save(ExportTask $task): void;

    /**
     * Find export task by ID.
     */
    public function findById(ExportTaskId $id): ?ExportTask;

    /**
     * Find all export tasks for a user.
     *
     * @return array<ExportTask>
     */
    public function findByUserId(int $userId): array;

    /**
     * Delete export task.
     */
    public function delete(ExportTaskId $id): void;
}

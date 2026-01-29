<?php

declare(strict_types=1);

namespace App\Domain\Import\Repositories;

use App\Domain\Import\Entities\ImportTask;
use App\Domain\Import\ValueObjects\ImportTaskId;

/**
 * Repository Interface: ImportTaskRepository
 *
 * Responsible for persisting and retrieving ImportTask aggregates.
 */
interface ImportTaskRepository
{
    /**
     * Save import task.
     */
    public function save(ImportTask $task): void;

    /**
     * Find import task by ID.
     */
    public function findById(ImportTaskId $id): ?ImportTask;

    /**
     * Find all import tasks for a user.
     *
     * @return array<ImportTask>
     */
    public function findByUserId(int $userId): array;

    /**
     * Delete import task.
     */
    public function delete(ImportTaskId $id): void;
}

<?php

declare(strict_types=1);

namespace App\Domain\Export\Events;

use App\Domain\DomainEvent;
use App\Domain\Export\ValueObjects\ExportTaskId;

/**
 * Event: Export task has completed.
 */
final class ExportCompleted extends DomainEvent
{
    public function __construct(
        public readonly ExportTaskId $taskId,
        public readonly string $filePath,
    ) {
        parent::__construct();
    }
}

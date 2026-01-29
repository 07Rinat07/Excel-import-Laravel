<?php

declare(strict_types=1);

namespace App\Domain\Export\Events;

use App\Domain\DomainEvent;
use App\Domain\Export\ValueObjects\ExportTaskId;

/**
 * Event: Export task has started.
 */
final class ExportStarted extends DomainEvent
{
    public function __construct(
        public readonly ExportTaskId $taskId,
    ) {
        parent::__construct();
    }
}

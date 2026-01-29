<?php

declare(strict_types=1);

namespace App\Domain\Import\Events;

use App\Domain\DomainEvent;
use App\Domain\Import\ValueObjects\ImportTaskId;

/**
 * Event: Import has completed successfully.
 */
final class ImportSucceeded extends DomainEvent
{
    public function __construct(
        public readonly ImportTaskId $importTaskId,
        public readonly int $rowsProcessed,
    ) {
        parent::__construct();
    }
}

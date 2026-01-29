<?php

declare(strict_types=1);

namespace App\Application\Import\Listeners;

use App\Domain\Import\Events\ImportStarted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Psr\Log\LoggerInterface;

/**
 * Listener: ImportStartedListener
 *
 * Handles ImportStarted domain event.
 * Logs import start for audit trail.
 */
final class ImportStartedListener implements ShouldQueue
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function handle(ImportStarted $event): void
    {
        $this->logger->info('Import started', [
            'import_task_id' => $event->importTaskId->value(),
            'occurred_at' => $event->occurredAt->format('Y-m-d H:i:s'),
        ]);
    }
}

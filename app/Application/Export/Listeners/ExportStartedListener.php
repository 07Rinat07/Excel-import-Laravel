<?php

declare(strict_types=1);

namespace App\Application\Export\Listeners;

use App\Domain\Export\Events\ExportStarted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Psr\Log\LoggerInterface;

/**
 * Listener: ExportStartedListener
 *
 * Handles ExportStarted domain event.
 * Logs export start.
 */
final class ExportStartedListener implements ShouldQueue
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function handle(ExportStarted $event): void
    {
        $this->logger->info('Export started', [
            'export_task_id' => $event->taskId->value(),
            'occurred_at' => $event->occurredAt->format('Y-m-d H:i:s'),
        ]);
    }
}

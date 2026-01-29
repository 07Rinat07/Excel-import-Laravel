<?php

declare(strict_types=1);

namespace App\Infrastructure\EventHandling\Export;

use App\Domain\Export\Events\ExportStarted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Psr\Log\LoggerInterface;

/**
 * Listener: ExportStartedListener
 *
 * Handles ExportStarted domain event.
 * Logs export start and prepares infrastructure.
 */
class ExportStartedListener implements ShouldQueue
{
    use SerializesModels;

    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function handle(ExportStarted $event): void
    {
        $this->logger->info('Export started', [
            'export_id' => $event->taskId->value(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Could trigger additional setup work here (e.g., queue worker preparation)
    }
}

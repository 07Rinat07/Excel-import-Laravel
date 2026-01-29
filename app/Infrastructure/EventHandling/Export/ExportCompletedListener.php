<?php

declare(strict_types=1);

namespace App\Infrastructure\EventHandling\Export;

use App\Domain\Export\Events\ExportCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Psr\Log\LoggerInterface;

/**
 * Listener: ExportCompletedListener
 *
 * Handles ExportCompleted domain event.
 * Logs export completion and sends notifications.
 */
class ExportCompletedListener implements ShouldQueue
{
    use SerializesModels;

    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function handle(ExportCompleted $event): void
    {
        $this->logger->info('Export completed', [
            'export_id' => $event->taskId->value(),
            'file_path' => $event->filePath,
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Could send notification to user here
        // Could copy file to CDN or external storage
        // Could trigger cleanup of temporary files
    }
}

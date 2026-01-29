<?php

declare(strict_types=1);

namespace App\Application\Import\Listeners;

use App\Domain\Import\Events\ImportFailed;
use App\Domain\Import\Repositories\ImportTaskRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Psr\Log\LoggerInterface;

/**
 * Listener: ImportFailedListener
 *
 * Handles ImportFailed domain event.
 * Logs failure, sends error notifications to user.
 */
final class ImportFailedListener implements ShouldQueue
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ImportTaskRepository $importTaskRepository,
    ) {}

    public function handle(ImportFailed $event): void
    {
        // Log the failure
        $this->logger->error('Import failed', [
            'import_task_id' => $event->importTaskId->value(),
            'reason' => $event->reason,
            'occurred_at' => $event->occurredAt->format('Y-m-d H:i:s'),
        ]);

        // Get import task details for notification
        $importTask = $this->importTaskRepository->findById($event->importTaskId);
        if (!$importTask) {
            $this->logger->warning('Import task not found for error notification', [
                'import_task_id' => $event->importTaskId->value(),
            ]);
            return;
        }

        // TODO: Send error notification to user
        // $this->notificationService->notifyImportFailure(
        //     $importTask->userId->value(),
        //     $event->reason
        // );

        // TODO: Clean up temporary files if needed
        // $this->fileService->cleanupImportFiles($event->importTaskId->value());
    }
}

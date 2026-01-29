<?php

declare(strict_types=1);

namespace App\Application\Import\Listeners;

use App\Domain\Import\Events\ImportSucceeded;
use App\Domain\Import\Repositories\ImportTaskRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Psr\Log\LoggerInterface;

/**
 * Listener: ImportSucceededListener
 *
 * Handles ImportSucceeded domain event.
 * Logs success, sends notifications to user.
 */
final class ImportSucceededListener implements ShouldQueue
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ImportTaskRepository $importTaskRepository,
    ) {}

    public function handle(ImportSucceeded $event): void
    {
        // Log the success
        $this->logger->info('Import succeeded', [
            'import_task_id' => $event->importTaskId->value(),
            'rows_processed' => $event->rowsProcessed,
            'occurred_at' => $event->occurredAt->format('Y-m-d H:i:s'),
        ]);

        // Get import task details for notification
        $importTask = $this->importTaskRepository->findById($event->importTaskId);
        if (!$importTask) {
            $this->logger->warning('Import task not found for notification', [
                'import_task_id' => $event->importTaskId->value(),
            ]);
            return;
        }

        // TODO: Send notification to user
        // $this->notificationService->notifyImportSuccess(
        //     $importTask->userId->value(),
        //     $event->rowsProcessed
        // );
    }
}

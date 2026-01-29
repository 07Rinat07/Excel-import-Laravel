<?php

declare(strict_types=1);

namespace App\Application\Export\Listeners;

use App\Domain\Export\Events\ExportCompleted;
use App\Domain\Export\Repositories\ExportTaskRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Psr\Log\LoggerInterface;

/**
 * Listener: ExportCompletedListener
 *
 * Handles ExportCompleted domain event.
 * Logs completion, sends notification to user with download link.
 */
final class ExportCompletedListener implements ShouldQueue
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ExportTaskRepository $exportTaskRepository,
    ) {}

    public function handle(ExportCompleted $event): void
    {
        // Log the completion
        $this->logger->info('Export completed', [
            'export_task_id' => $event->taskId->value(),
            'file_path' => $event->filePath,
            'occurred_at' => $event->occurredAt->format('Y-m-d H:i:s'),
        ]);

        // Get export task details for notification
        $exportTask = $this->exportTaskRepository->findById($event->taskId);
        if (!$exportTask) {
            $this->logger->warning('Export task not found for notification', [
                'export_task_id' => $event->exportTaskId->value(),
            ]);
            return;
        }

        // TODO: Send notification to user with download link
        // $this->notificationService->notifyExportReady(
        //     $exportTask->userId->value(),
        //     $event->filePath,
        //     $exportTask->format->extension()
        // );
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Export\Entities;

use App\Domain\DomainEntity;
use App\Domain\Export\ValueObjects\ExportTaskId;
use App\Domain\Export\ValueObjects\ExportFormat;
use App\Domain\Export\ValueObjects\ExportColumns;
use App\Domain\Export\ValueObjects\ExportStatus;
use App\Domain\Import\ValueObjects\UserId;
use App\Domain\Import\ValueObjects\TemplateId;
use App\Domain\Export\Events\ExportStarted;
use App\Domain\Export\Events\ExportCompleted;
use DateTimeImmutable;

/**
 * Entity: ExportTask - Aggregate Root for export operations.
 */
final class ExportTask extends DomainEntity
{
    private function __construct(
        public readonly ExportTaskId $id,
        public readonly ExportFormat $format,
        public readonly ExportColumns $columns,
        public readonly UserId $userId,
        public readonly TemplateId $templateId,
        public ExportStatus $status,
        public readonly DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $completedAt = null,
        public ?string $filePath = null,
        public ?string $failureReason = null,
    ) {}

    public static function create(
        ExportTaskId $id,
        ExportFormat $format,
        ExportColumns $columns,
        UserId $userId,
        TemplateId $templateId,
    ): self {
        return new self(
            id: $id,
            format: $format,
            columns: $columns,
            userId: $userId,
            templateId: $templateId,
            status: ExportStatus::pending(),
            createdAt: new DateTimeImmutable(),
        );
    }

    public static function restore(
        ExportTaskId $id,
        ExportFormat $format,
        ExportColumns $columns,
        UserId $userId,
        TemplateId $templateId,
        ExportStatus $status,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $completedAt = null,
        ?string $filePath = null,
        ?string $failureReason = null,
    ): self {
        return new self(
            id: $id,
            format: $format,
            columns: $columns,
            userId: $userId,
            templateId: $templateId,
            status: $status,
            createdAt: $createdAt,
            completedAt: $completedAt,
            filePath: $filePath,
            failureReason: $failureReason,
        );
    }

    public function start(): void
    {
        if (!$this->status->isPending()) {
            throw new \InvalidArgumentException('Only pending exports can be started');
        }

        $this->status = ExportStatus::processing();
        $this->recordEvent(new ExportStarted($this->id));
    }

    public function complete(string $filePath): void
    {
        if (!$this->status->isProcessing()) {
            throw new \InvalidArgumentException('Only processing exports can be completed');
        }

        $this->status = ExportStatus::completed();
        $this->filePath = $filePath;
        $this->completedAt = new DateTimeImmutable();
        $this->recordEvent(new ExportCompleted($this->id, $filePath));
    }

    public function fail(string $reason): void
    {
        if ($this->status->isFailed()) {
            throw new \InvalidArgumentException('Export already failed');
        }

        $this->status = ExportStatus::failed();
        $this->failureReason = $reason;
        $this->completedAt = new DateTimeImmutable();
    }

    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    public function isProcessing(): bool
    {
        return $this->status->isProcessing();
    }

    public function isCompleted(): bool
    {
        return $this->status->isCompleted();
    }

    public function isFailed(): bool
    {
        return $this->status->isFailed();
    }
}

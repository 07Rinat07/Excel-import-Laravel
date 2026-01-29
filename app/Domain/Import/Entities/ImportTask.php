<?php

declare(strict_types=1);

namespace App\Domain\Import\Entities;

use App\Domain\DomainEntity;
use App\Domain\Import\ValueObjects\ImportTaskId;
use App\Domain\Import\ValueObjects\FileName;
use App\Domain\Import\ValueObjects\ImportStatus;
use App\Domain\Import\ValueObjects\UserId;
use App\Domain\Import\ValueObjects\TemplateId;
use App\Domain\Import\ValueObjects\MappingStrategy;
use App\Domain\Import\Events\ImportStarted;
use App\Domain\Import\Events\ImportSucceeded;
use App\Domain\Import\Events\ImportFailed;
use DateTimeImmutable;

/**
 * Entity: ImportTask - Aggregate Root for import operations.
 */
final class ImportTask extends DomainEntity
{
    private function __construct(
        public readonly ImportTaskId $id,
        public readonly FileName $fileName,
        public readonly UserId $userId,
        public readonly TemplateId $templateId,
        public ImportStatus $status,
        public readonly MappingStrategy $mappingStrategy,
        public readonly DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $completedAt = null,
        public ?string $failureReason = null,
        public int $rowsProcessed = 0,
    ) {}

    public static function create(
        ImportTaskId $id,
        FileName $fileName,
        UserId $userId,
        TemplateId $templateId,
        MappingStrategy $mappingStrategy,
    ): self {
        $task = new self(
            id: $id,
            fileName: $fileName,
            userId: $userId,
            templateId: $templateId,
            status: ImportStatus::pending(),
            mappingStrategy: $mappingStrategy,
            createdAt: new DateTimeImmutable(),
        );

        return $task;
    }

    /**
     * Restore an ImportTask from persisted state (e.g., from database)
     */
    public static function restore(
        ImportTaskId $id,
        FileName $fileName,
        UserId $userId,
        TemplateId $templateId,
        MappingStrategy $mappingStrategy,
        ImportStatus $status,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $completedAt = null,
        ?string $failureReason = null,
        int $rowsProcessed = 0,
    ): self {
        return new self(
            id: $id,
            fileName: $fileName,
            userId: $userId,
            templateId: $templateId,
            status: $status,
            mappingStrategy: $mappingStrategy,
            createdAt: $createdAt,
            completedAt: $completedAt,
            failureReason: $failureReason,
            rowsProcessed: $rowsProcessed,
        );
    }

    public function start(): void
    {
        if (!$this->status->isPending()) {
            throw new \InvalidArgumentException('Only pending imports can be started');
        }

        $this->status = ImportStatus::processing();
        $this->recordEvent(new ImportStarted($this->id));
    }

    public function succeed(int $rowsProcessed): void
    {
        if (!$this->status->isProcessing()) {
            throw new \InvalidArgumentException('Only processing imports can succeed');
        }

        $this->status = ImportStatus::succeeded();
        $this->rowsProcessed = $rowsProcessed;
        $this->completedAt = new DateTimeImmutable();
        $this->recordEvent(new ImportSucceeded($this->id, $rowsProcessed));
    }

    public function fail(string $reason): void
    {
        if ($this->status->isFailed()) {
            throw new \InvalidArgumentException('Import already failed');
        }

        $this->status = ImportStatus::failed();
        $this->failureReason = $reason;
        $this->completedAt = new DateTimeImmutable();
        $this->recordEvent(new ImportFailed($this->id, $reason));
    }

    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    public function isProcessing(): bool
    {
        return $this->status->isProcessing();
    }

    public function isSucceeded(): bool
    {
        return $this->status->isSucceeded();
    }

    public function isFailed(): bool
    {
        return $this->status->isFailed();
    }

    public function isCompleted(): bool
    {
        return $this->completedAt !== null;
    }
}

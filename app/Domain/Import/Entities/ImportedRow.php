<?php

declare(strict_types=1);

namespace App\Domain\Import\Entities;

use App\Domain\DomainEntity;
use App\Domain\Import\ValueObjects\RowNumber;
use App\Domain\Import\ValueObjects\Row;
use App\Domain\Import\ValueObjects\ValidationResult;
use DateTimeImmutable;

/**
 * Entity: ImportedRow - Represents a single imported row.
 */
final class ImportedRow extends DomainEntity
{
    private function __construct(
        public readonly int $id,
        public readonly int $importTaskId,
        public readonly RowNumber $rowNumber,
        public readonly Row $data,
        public readonly ValidationResult $validationResult,
        public readonly DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $processedAt = null,
    ) {}

    public static function create(
        int $importTaskId,
        RowNumber $rowNumber,
        Row $data,
        ValidationResult $validationResult,
    ): self {
        return new self(
            id: 0, // Will be assigned by persistence layer
            importTaskId: $importTaskId,
            rowNumber: $rowNumber,
            data: $data,
            validationResult: $validationResult,
            createdAt: new DateTimeImmutable(),
        );
    }

    public function isValid(): bool
    {
        return $this->validationResult->isValid();
    }

    public function getErrors(): array
    {
        return $this->validationResult->errors();
    }

    public function markAsProcessed(): void
    {
        if ($this->processedAt !== null) {
            throw new \InvalidArgumentException('Row already processed');
        }
    }
}

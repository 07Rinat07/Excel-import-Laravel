<?php

declare(strict_types=1);

namespace App\Domain\Export\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Selected columns for export.
 */
final class ExportColumns extends ValueObject
{
    /**
     * @param array<int> $columnIds List of selected column IDs
     */
    public function __construct(
        public readonly array $columnIds,
    ) {
        if (empty($this->columnIds)) {
            throw new \InvalidArgumentException('At least one column must be selected for export');
        }
    }

    /**
     * @param array<int> $columnIds
     */
    public static function from(array $columnIds): self
    {
        return new self($columnIds);
    }

    /**
     * @return array<int>
     */
    public function ids(): array
    {
        return $this->columnIds;
    }

    /**
     * @return array<int>
     */
    public function toArray(): array
    {
        return $this->columnIds;
    }

    public function count(): int
    {
        return count($this->columnIds);
    }

    public function hasColumn(int $columnId): bool
    {
        return in_array($columnId, $this->columnIds, true);
    }

    public function equals(ValueObject $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        sort($this->columnIds);
        $otherIds = $other->columnIds;
        sort($otherIds);
        return $this->columnIds === $otherIds;
    }

    public function toString(): string
    {
        return implode(',', $this->columnIds);
    }
}

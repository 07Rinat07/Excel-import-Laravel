<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Row number and position in file.
 */
final class RowNumber extends ValueObject
{
    public function __construct(
        public readonly int $value,
    ) {
        if ($this->value <= 0) {
            throw new \InvalidArgumentException('Row number must be positive');
        }
    }

    public static function from(int $rowNumber): self
    {
        return new self($rowNumber);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function isHeaderRow(): bool
    {
        return $this->value === 1;
    }

    public function isDataRow(): bool
    {
        return $this->value > 1;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }
}

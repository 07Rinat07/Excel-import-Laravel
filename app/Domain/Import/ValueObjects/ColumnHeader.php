<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Column header from Excel file.
 */
final class ColumnHeader extends ValueObject
{
    public function __construct(
        public readonly string $name,
        public readonly int $position,
    ) {
        if (empty(trim($this->name))) {
            throw new \InvalidArgumentException('Column name cannot be empty');
        }
        if ($this->position < 0) {
            throw new \InvalidArgumentException('Column position cannot be negative');
        }
    }

    public static function from(string $name, int $position): self
    {
        return new self($name, $position);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->name === $other->name
            && $this->position === $other->position;
    }

    public function toString(): string
    {
        return "{$this->name} (position: {$this->position})";
    }
}

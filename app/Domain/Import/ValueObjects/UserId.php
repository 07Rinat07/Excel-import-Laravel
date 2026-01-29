<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: User identifier.
 */
final class UserId extends ValueObject
{
    public function __construct(
        public readonly int $value,
    ) {
        if ($this->value <= 0) {
            throw new \InvalidArgumentException('UserId must be a positive integer');
        }
    }

    public static function from(int $userId): self
    {
        return new self($userId);
    }

    public function value(): int
    {
        return $this->value;
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

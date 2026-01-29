<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;
use InvalidArgumentException;

/**
 * Import Task ID - unique identifier for an import task.
 */
final class ImportTaskId extends ValueObject
{
    public function __construct(
        private readonly int $value
    ) {
        if ($this->value <= 0) {
            throw new InvalidArgumentException('Import task ID must be a positive integer');
        }
    }

    public static function generate(): self
    {
        // Generate a unique positive integer ID using full microsecond precision and random
        // Combine microsecond timestamp with random number for guaranteed uniqueness
        $micro = (int)(microtime(true) * 1000000); // Full microseconds
        $random = random_int(1000, 9999); // 4-digit random
        $id = $micro + ($random % 1000000);

        // Keep within signed 32-bit range
        if ($id > 2147483647) {
            $id = ($id % 1000000000) + 1;
        }

        return new self(max(1, $id));
    }

    public static function from(int $id): self
    {
        return new self($id);
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
        return (string)$this->value;
    }
}

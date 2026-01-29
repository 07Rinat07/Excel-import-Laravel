<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Row - represents a single row of data from imported file.
 */
final class Row extends ValueObject
{
    public function __construct(
        private readonly array $data
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function keys(): array
    {
        return array_keys($this->data);
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->data === $other->data;
    }

    public function toString(): string
    {
        return json_encode($this->data);
    }
}

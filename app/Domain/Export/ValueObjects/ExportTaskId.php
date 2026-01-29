<?php

declare(strict_types=1);

namespace App\Domain\Export\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Export task identifier (UUID).
 */
final class ExportTaskId extends ValueObject
{
    public function __construct(
        public readonly string $value,
    ) {
        if (empty($this->value)) {
            throw new \InvalidArgumentException('ExportTaskId cannot be empty');
        }
    }

    public static function from(string $id): self
    {
        return new self($id);
    }

    public static function generate(): self
    {
        // Generate a UUID v4
        return new self(sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        ));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;
use InvalidArgumentException;

/**
 * File Name - represents the name of an uploaded file.
 */
final class FileName extends ValueObject
{
    public function __construct(
        private readonly string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('File name cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('File name cannot exceed 255 characters');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function extension(): string
    {
        $parts = explode('.', $this->value);
        return strtolower(end($parts));
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

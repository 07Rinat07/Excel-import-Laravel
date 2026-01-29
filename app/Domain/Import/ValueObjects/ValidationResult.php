<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;
use InvalidArgumentException;

/**
 * Validation Result - represents the result of data validation.
 */
final class ValidationResult extends ValueObject
{
    private function __construct(
        private readonly bool $valid,
        private readonly array $errors = [],
    ) {
    }

    public static function success(): self
    {
        return new self(true);
    }

    public static function failed(array $errors): self
    {
        if (empty($errors)) {
            throw new InvalidArgumentException('Failed validation result must have errors');
        }
        return new self(false, $errors);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self &&
               $this->valid === $other->valid &&
               $this->errors === $other->errors;
    }

    public function toString(): string
    {
        return $this->valid ? 'VALID' : 'INVALID: ' . json_encode($this->errors);
    }
}

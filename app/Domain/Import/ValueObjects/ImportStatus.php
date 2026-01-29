<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Import status (pending, processing, succeeded, failed).
 */
final class ImportStatus extends ValueObject
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const SUCCEEDED = 'succeeded';
    public const FAILED = 'failed';

    private function __construct(
        public readonly string $value,
    ) {}

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function succeeded(): self
    {
        return new self(self::SUCCEEDED);
    }

    public static function failed(): self
    {
        return new self(self::FAILED);
    }

    public static function from(string $status): self
    {
        return match ($status) {
            self::PENDING => self::pending(),
            self::PROCESSING => self::processing(),
            self::SUCCEEDED => self::succeeded(),
            self::FAILED => self::failed(),
            default => throw new \InvalidArgumentException("Invalid status: {$status}"),
        };
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->value === self::PROCESSING;
    }

    public function isSucceeded(): bool
    {
        return $this->value === self::SUCCEEDED;
    }

    public function isFailed(): bool
    {
        return $this->value === self::FAILED;
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

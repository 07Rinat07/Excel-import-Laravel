<?php

declare(strict_types=1);

namespace App\Domain\Export\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Export status.
 */
final class ExportStatus extends ValueObject
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const COMPLETED = 'completed';
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

    public static function completed(): self
    {
        return new self(self::COMPLETED);
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
            self::COMPLETED => self::completed(),
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

    public function isCompleted(): bool
    {
        return $this->value === self::COMPLETED;
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

<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Template identifier for Excel import templates.
 */
final class TemplateId extends ValueObject
{
    public function __construct(
        public readonly int $value,
    ) {
        if ($this->value <= 0) {
            throw new \InvalidArgumentException('TemplateId must be a positive integer');
        }
    }

    public static function from(int $templateId): self
    {
        return new self($templateId);
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

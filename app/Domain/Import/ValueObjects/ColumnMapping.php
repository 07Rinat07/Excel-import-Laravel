<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Column mapping from source Excel column to target field.
 */
final class ColumnMapping extends ValueObject
{
    public function __construct(
        public readonly ColumnHeader $source,
        public readonly string $targetField,
        public readonly bool $required = false,
    ) {
        if (empty(trim($this->targetField))) {
            throw new \InvalidArgumentException('Target field cannot be empty');
        }
    }

    public static function from(ColumnHeader $source, string $targetField, bool $required = false): self
    {
        return new self($source, $targetField, $required);
    }

    public function source(): ColumnHeader
    {
        return $this->source;
    }

    public function targetField(): string
    {
        return $this->targetField;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->source->equals($other->source)
            && $this->targetField === $other->targetField
            && $this->required === $other->required;
    }

    public function toString(): string
    {
        $required = $this->required ? ' (required)' : '';
        return "{$this->source->name()} → {$this->targetField}{$required}";
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Mapping strategy for how columns are mapped.
 */
final class MappingStrategy extends ValueObject
{
    public const EXACT = 'exact';
    public const FUZZY = 'fuzzy';
    public const MANUAL = 'manual';
    public const TEMPLATE = 'template';

    private function __construct(
        public readonly string $value,
    ) {}

    public static function exact(): self
    {
        return new self(self::EXACT);
    }

    public static function fuzzy(): self
    {
        return new self(self::FUZZY);
    }

    public static function manual(): self
    {
        return new self(self::MANUAL);
    }

    public static function template(): self
    {
        return new self(self::TEMPLATE);
    }

    public static function from(string $strategy): self
    {
        return match ($strategy) {
            self::EXACT => self::exact(),
            self::FUZZY => self::fuzzy(),
            self::MANUAL => self::manual(),
            self::TEMPLATE => self::template(),
            default => throw new \InvalidArgumentException("Invalid strategy: {$strategy}"),
        };
    }

    public function isExact(): bool
    {
        return $this->value === self::EXACT;
    }

    public function isFuzzy(): bool
    {
        return $this->value === self::FUZZY;
    }

    public function isManual(): bool
    {
        return $this->value === self::MANUAL;
    }

    public function isTemplate(): bool
    {
        return $this->value === self::TEMPLATE;
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

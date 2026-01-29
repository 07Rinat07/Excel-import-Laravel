<?php

declare(strict_types=1);

namespace App\Domain\Import\ValueObjects;

use App\Domain\ValueObject;

/**
 * Value Object: Validation rule for a column/field.
 */
final class ValidationRule extends ValueObject
{
    public const REQUIRED = 'required';
    public const NUMERIC = 'numeric';
    public const STRING = 'string';
    public const EMAIL = 'email';
    public const MAX_LENGTH = 'max_length';
    public const MIN_LENGTH = 'min_length';
    public const PATTERN = 'pattern';
    public const ENUM = 'enum';
    public const UNIQUE = 'unique';

    public function __construct(
        public readonly string $type,
        public readonly mixed $value = null,
    ) {
        if (!$this->isValidType($type)) {
            throw new \InvalidArgumentException("Invalid validation rule type: {$type}");
        }
    }

    public static function required(): self
    {
        return new self(self::REQUIRED);
    }

    public static function numeric(): self
    {
        return new self(self::NUMERIC);
    }

    public static function string(): self
    {
        return new self(self::STRING);
    }

    public static function email(): self
    {
        return new self(self::EMAIL);
    }

    public static function maxLength(int $length): self
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('Max length must be positive');
        }
        return new self(self::MAX_LENGTH, $length);
    }

    public static function minLength(int $length): self
    {
        if ($length < 0) {
            throw new \InvalidArgumentException('Min length cannot be negative');
        }
        return new self(self::MIN_LENGTH, $length);
    }

    public static function pattern(string $regex): self
    {
        if (empty($regex)) {
            throw new \InvalidArgumentException('Pattern cannot be empty');
        }
        return new self(self::PATTERN, $regex);
    }

    public static function enum(array $allowedValues): self
    {
        if (empty($allowedValues)) {
            throw new \InvalidArgumentException('Enum values cannot be empty');
        }
        return new self(self::ENUM, $allowedValues);
    }

    public static function unique(): self
    {
        return new self(self::UNIQUE);
    }

    private function isValidType(string $type): bool
    {
        return in_array($type, [
            self::REQUIRED,
            self::NUMERIC,
            self::STRING,
            self::EMAIL,
            self::MAX_LENGTH,
            self::MIN_LENGTH,
            self::PATTERN,
            self::ENUM,
            self::UNIQUE,
        ], true);
    }

    public function type(): string
    {
        return $this->type;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && $this->type === $other->type
            && $this->value === $other->value;
    }

    public function toString(): string
    {
        if ($this->value === null) {
            return $this->type;
        }
        return "{$this->type}({$this->value})";
    }
}

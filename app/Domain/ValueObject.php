<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Base class for all value objects.
 * Value objects represent immutable concepts without identity.
 */
abstract class ValueObject
{
    /**
     * Check equality with another value object.
     */
    abstract public function equals(ValueObject $other): bool;

    /**
     * Get string representation of value object.
     */
    abstract public function toString(): string;

    public function __toString(): string
    {
        return $this->toString();
    }
}

<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Base class for all domain events.
 * Domain events represent something important that has happened in the domain.
 */
abstract class DomainEvent
{
    public function __construct(
        public readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {}
}

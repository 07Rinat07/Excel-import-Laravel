<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Base class for all domain entities.
 * Entities have identity and can record domain events.
 */
abstract class DomainEntity
{
    /**
     * @var DomainEvent[]
     */
    protected array $events = [];

    /**
     * Record a domain event.
     */
    public function recordEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    /**
     * Get and clear all recorded events.
     *
     * @return DomainEvent[]
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}

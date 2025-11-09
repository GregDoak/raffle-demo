<?php

declare(strict_types=1);

namespace App\Tests\Double\Framework\Domain\Event;

use App\Foundation\DomainEventRegistry\DomainEventInterface;
use App\Framework\Domain\Event\DomainEventBusInterface;

final class DomainEventBusSpy implements DomainEventBusInterface
{
    /** @var DomainEventInterface[] */
    private array $events = [];

    public function publish(DomainEventInterface $event): void
    {
        $this->events[] = $event;
    }

    /** @return DomainEventInterface[] */
    public function getEvents(): array
    {
        return $this->events;
    }
}

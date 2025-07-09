<?php

declare(strict_types=1);

namespace App\Framework\Domain\Event;

use App\Foundation\Clock\Clock;
use DateTimeInterface;

abstract readonly class AbstractDomainEvent implements DomainEventInterface
{
    public DateTimeInterface $occurredAt;

    public function __construct()
    {
        $this->occurredAt = Clock::now();
    }

    abstract public function serialize(): string;
}

<?php

declare(strict_types=1);

namespace App\Application\Mono\Framework\Domain\Event;

use Alleava\Foundation\Clock\Clock;
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

<?php

declare(strict_types=1);

namespace App\Foundation\DomainEventRegistry;

use DateTimeInterface;

interface DomainEventInterface
{
    public function getEventId(): string;

    public function getEventOccurredAt(): DateTimeInterface;

    public function getEventType(): string;

    /** @return mixed[] */
    public function serialize(): array;
}

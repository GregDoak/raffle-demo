<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model\Event;

use DomainException;

interface AggregateEventFactoryInterface
{
    /** @throws DomainException */
    public function fromSerialized(string $eventName, string $eventPayload): AggregateEventInterface;
}

<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model\Event;

use App\Framework\Domain\Exception\AggregateEventNotHandledException;

interface AggregateEventFactoryInterface
{
    /** @throws AggregateEventNotHandledException */
    public function fromSerialized(string $eventName, string $eventPayload): AggregateEventInterface;
}

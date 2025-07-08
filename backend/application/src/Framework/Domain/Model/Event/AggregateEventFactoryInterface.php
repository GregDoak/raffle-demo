<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model\Event;

interface AggregateEventFactoryInterface
{
    public static function fromSerialized(string $aggregateName, string $eventName, string $eventPayload): AggregateEventInterface;
}

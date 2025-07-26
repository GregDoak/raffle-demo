<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Model\Event;

use App\Framework\Domain\Model\Event\AggregateEventFactoryInterface;
use App\Framework\Domain\Model\Event\AggregateEventInterface;
use DomainException;

use function sprintf;

final readonly class RaffleEventFactory implements AggregateEventFactoryInterface
{
    public function fromSerialized(
        string $eventName,
        string $eventPayload,
    ): AggregateEventInterface {
        return match ($eventName) {
            PrizeDrawn::EVENT_NAME => PrizeDrawn::deserialize($eventPayload),
            RaffleClosed::EVENT_NAME => RaffleClosed::deserialize($eventPayload),
            RaffleCreated::EVENT_NAME => RaffleCreated::deserialize($eventPayload),
            RaffleStarted::EVENT_NAME => RaffleStarted::deserialize($eventPayload),
            TicketAllocatedToParticipant::EVENT_NAME => TicketAllocatedToParticipant::deserialize($eventPayload),
            default => throw new DomainException(sprintf("Unable to build event '%s'", $eventName)),
        };
    }
}

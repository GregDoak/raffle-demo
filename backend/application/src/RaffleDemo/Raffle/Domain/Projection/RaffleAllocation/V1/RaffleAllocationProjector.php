<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1;

use App\Framework\Domain\Model\AggregateEvents;
use App\Framework\Domain\Projection\AggregateEventsSubscriberInterface;
use App\RaffleDemo\Raffle\Domain\Model\Event\TicketAllocatedToParticipant;

final readonly class RaffleAllocationProjector implements AggregateEventsSubscriberInterface
{
    public function __construct(
        private RaffleAllocationProjectionRepositoryInterface $repository,
    ) {
    }

    public function __invoke(AggregateEvents $events): void
    {
        foreach ($events as $event) {
            match ($event::class) {
                TicketAllocatedToParticipant::class => $this->handleTicketAllocatedToParticipant($event),
                default => null,
            };
        }
    }

    private function handleTicketAllocatedToParticipant(TicketAllocatedToParticipant $event): void
    {
        $this->repository->store(
            new RaffleAllocation(
                raffleId: $event->getAggregateId()->toString(),
                hash: $event->ticketAllocation->hash,
                allocatedAt: $event->ticketAllocation->allocatedAt,
                allocatedTo: $event->ticketAllocation->allocatedTo,
                quantity: $event->ticketAllocation->quantity,
                lastOccurredAt: $event->occurredAt->toDateTime(),
            ),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\Raffle;

use App\Framework\Domain\Model\AggregateEvents;
use App\Framework\Domain\Projection\AggregateEventsSubscriberInterface;
use App\RaffleDemo\Raffle\Domain\Model\Event\PrizeDrawn;
use App\RaffleDemo\Raffle\Domain\Model\Event\RaffleClosed;
use App\RaffleDemo\Raffle\Domain\Model\Event\RaffleCreated;
use App\RaffleDemo\Raffle\Domain\Model\Event\RaffleStarted;
use App\RaffleDemo\Raffle\Domain\Model\Event\TicketAllocatedToParticipant;

final readonly class RaffleProjector implements AggregateEventsSubscriberInterface
{
    public function __construct(
        private RaffleProjectionRepositoryInterface $repository,
    ) {
    }

    public function __invoke(AggregateEvents $events): void
    {
        foreach ($events as $event) {
            match ($event::class) {
                RaffleCreated::class => $this->handleRaffleCreated($event),
                RaffleStarted::class => $this->handleRaffleStarted($event),
                TicketAllocatedToParticipant::class => $this->handleTicketAllocatedToParticipant($event),
                RaffleClosed::class => $this->handleRaffleClosed($event),
                PrizeDrawn::class => $this->handlePrizeDrawn($event),
                default => null,
            };
        }
    }

    private function handleRaffleCreated(RaffleCreated $event): void
    {
        $raffle = Raffle::fromCreated(
            id: $event->getAggregateId()->toString(),
            name: $event->name->toString(),
            prize: $event->prize->toString(),
            createdAt: $event->created->at,
            createdBy: $event->created->by,
            startAt: $event->startAt->toDateTime(),
            totalTickets: $event->totalTickets->toInt(),
            remainingTickets: $event->totalTickets->toInt(),
            ticketAmount: $event->ticketPrice->amount,
            ticketCurrency: $event->ticketPrice->currency,
            closeAt: $event->closeAt->toDateTime(),
            drawAt: $event->drawAt->toDateTime(),
            lastOccurredAt: $event->occurredAt->toDateTime(),
        );

        $this->repository->store($raffle);
    }

    private function handleRaffleStarted(RaffleStarted $event): void
    {
        $raffle = $this->repository->getById($event->getAggregateId()->toString());

        if ($raffle === null) {
            return;
        }

        $raffle = $raffle->started(
            startedAt: $event->started->at,
            startedBy: $event->started->by,
            lastOccurredAt: $event->occurredAt->toDateTime(),
        );

        $this->repository->store($raffle);
    }

    private function handleTicketAllocatedToParticipant(TicketAllocatedToParticipant $event): void
    {
        $raffle = $this->repository->getById($event->getAggregateId()->toString());

        if ($raffle === null) {
            return;
        }

        $raffle = $raffle->ticketAllocated(
            ticketAllocationQuantity: $event->ticketAllocation->quantity,
            lastOccurredAt: $event->occurredAt->toDateTime(),
        );

        $this->repository->store($raffle);
    }

    private function handleRaffleClosed(RaffleClosed $event): void
    {
        $raffle = $this->repository->getById($event->getAggregateId()->toString());

        if ($raffle === null) {
            return;
        }

        $raffle = $raffle->closed(
            closedAt: $event->closed->at,
            closedBy: $event->closed->by,
            lastOccurredAt: $event->occurredAt->toDateTime(),
        );

        $this->repository->store($raffle);
    }

    private function handlePrizeDrawn(PrizeDrawn $event): void
    {
        $raffle = $this->repository->getById($event->getAggregateId()->toString());

        if ($raffle === null) {
            return;
        }

        $raffle = $raffle->drawn(
            drawnAt: $event->drawn->at,
            drawnBy: $event->drawn->by,
            winningAllocation: $event->winner->ticketAllocation->hash,
            winningTicketNumber: $event->winner->ticketNumber,
            wonBy: $event->winner->ticketAllocation->allocatedTo,
            lastOccurredAt: $event->occurredAt->toDateTime(),
        );

        $this->repository->store($raffle);
    }
}

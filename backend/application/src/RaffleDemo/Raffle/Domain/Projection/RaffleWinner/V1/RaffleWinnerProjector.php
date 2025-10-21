<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1;

use App\Framework\Domain\Model\AggregateEvents;
use App\Framework\Domain\Projection\AggregateEventsSubscriberInterface;
use App\RaffleDemo\Raffle\Domain\Model\Event\PrizeDrawn;

final readonly class RaffleWinnerProjector implements AggregateEventsSubscriberInterface
{
    public function __construct(
        private RaffleWinnerProjectionRepositoryInterface $repository,
    ) {
    }

    public function __invoke(AggregateEvents $events): void
    {
        foreach ($events as $event) {
            match ($event::class) {
                PrizeDrawn::class => $this->handlePrizeDrawn($event),
                default => null,
            };
        }
    }

    private function handlePrizeDrawn(PrizeDrawn $event): void
    {
        $this->repository->store(
            new RaffleWinner(
                raffleId: $event->getAggregateId()->toString(),
                raffleAllocationHash: $event->winner->ticketAllocation->hash,
                drawnAt: $event->drawn->at,
                winningTicketNumber: $event->winner->ticketNumber,
                winner: $event->winner->ticketAllocation->allocatedTo,
                lastOccurredAt: $event->occurredAt->toDateTime(),
            ),
        );
    }
}

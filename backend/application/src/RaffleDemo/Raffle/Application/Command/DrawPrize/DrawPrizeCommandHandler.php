<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\DrawPrize;

use App\Foundation\DomainEventRegistry\Raffle\RaffleDrawnV1Event;
use App\Foundation\DomainEventRegistry\Raffle\RaffleEndedV1Event;
use App\Framework\Application\Command\CommandHandlerInterface;
use App\Framework\Domain\Event\DomainEventBusInterface;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use Throwable;

final readonly class DrawPrizeCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionBoundaryInterface $transactionBoundary,
        private RaffleEventStoreRepository $repository,
        private DomainEventBusInterface $domainEventBus,
    ) {
    }

    public function __invoke(DrawPrizeCommand $command): void
    {
        try {
            $raffle = $this->repository->get($command->id);
            $raffle->drawPrize($command->drawn);

            $this->transactionBoundary->begin();
            $this->repository->store($raffle);

            $this->domainEventBus->publish(RaffleDrawnV1Event::fromNew(
                id: $raffle->getAggregateId()->toString(),
                name: $raffle->name->toString(),
                prize: $raffle->prize->toString(),
                createdAt: $raffle->created->at,
                createdBy: $raffle->created->by,
                startAt: $raffle->startAt->toDateTime(),
                closeAt: $raffle->closeAt->toDateTime(),
                drawAt: $raffle->drawAt->toDateTime(),
                drawnAt: $raffle->closed->at, // @phpstan-ignore-line argument.type
                drawnBy: $raffle->closed->by, // @phpstan-ignore-line argument.type
                totalTickets: $raffle->totalTickets->toInt(),
                winningTicketNumber: $raffle->winner->ticketNumber, // @phpstan-ignore-line argument.type
                winningAllocationTo: $raffle->winner->ticketAllocation->allocatedTo, // @phpstan-ignore-line argument.type
                ticketAmount: $raffle->ticketPrice->amount,
                ticketCurrency: $raffle->ticketPrice->currency,
            ));

            $this->domainEventBus->publish(
                RaffleEndedV1Event::fromNew(
                    id: $raffle->getAggregateId()->toString(),
                    name: $raffle->name->toString(),
                    prize: $raffle->prize->toString(),
                    createdAt: $raffle->created->at,
                    createdBy: $raffle->created->by,
                    startAt: $raffle->startAt->toDateTime(),
                    closeAt: $raffle->closeAt->toDateTime(),
                    drawAt: $raffle->drawAt->toDateTime(),
                    endedAt: $raffle->ended->at, // @phpstan-ignore-line argument.type
                    endedBy: $raffle->ended->by, // @phpstan-ignore-line argument.type
                    endedReason: $raffle->ended->reason, // @phpstan-ignore-line argument.type
                    totalTickets: $raffle->totalTickets->toInt(),
                    ticketAmount: $raffle->ticketPrice->amount,
                    ticketCurrency: $raffle->ticketPrice->currency,
                ),
            );

            $this->transactionBoundary->commit();
        } catch (Throwable $exception) {
            $this->transactionBoundary->rollback();

            throw $exception;
        }
    }
}

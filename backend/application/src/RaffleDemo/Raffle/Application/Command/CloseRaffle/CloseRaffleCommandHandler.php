<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\CloseRaffle;

use App\Foundation\DomainEventRegistry\Raffle\RaffleClosedV1Event;
use App\Foundation\DomainEventRegistry\Raffle\RaffleEndedV1Event;
use App\Framework\Application\Command\CommandHandlerInterface;
use App\Framework\Domain\Event\DomainEventBusInterface;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use Throwable;

final readonly class CloseRaffleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionBoundaryInterface $transactionBoundary,
        private RaffleEventStoreRepository $repository,
        private DomainEventBusInterface $domainEventBus,
    ) {
    }

    public function __invoke(CloseRaffleCommand $command): void
    {
        try {
            $raffle = $this->repository->get($command->id);
            $raffle->close($command->closed);

            $this->transactionBoundary->begin();
            $this->repository->store($raffle);

            $this->domainEventBus->publish(RaffleClosedV1Event::fromNew(
                id: $raffle->getAggregateId()->toString(),
                name: $raffle->name->toString(),
                prize: $raffle->prize->toString(),
                createdAt: $raffle->created->at,
                createdBy: $raffle->created->by,
                startAt: $raffle->startAt->toDateTime(),
                closeAt: $raffle->closeAt->toDateTime(),
                closedAt: $raffle->closed->at, // @phpstan-ignore-line argument.type
                closedBy: $raffle->closed->by, // @phpstan-ignore-line argument.type
                drawAt: $raffle->drawAt->toDateTime(),
                totalTickets: $raffle->totalTickets->toInt(),
                numberOfTicketsAllocated: $raffle->ticketAllocations->numberOfTicketsAllocated,
                ticketAmount: $raffle->ticketPrice->amount,
                ticketCurrency: $raffle->ticketPrice->currency,
            ));

            if ($raffle->ended !== null) {
                $this->domainEventBus->publish(RaffleEndedV1Event::fromNew(
                    id: $raffle->getAggregateId()->toString(),
                    name: $raffle->name->toString(),
                    prize: $raffle->prize->toString(),
                    createdAt: $raffle->created->at,
                    createdBy: $raffle->created->by,
                    startAt: $raffle->startAt->toDateTime(),
                    closeAt: $raffle->closeAt->toDateTime(),
                    drawAt: $raffle->drawAt->toDateTime(),
                    endedAt: $raffle->ended->at,
                    endedBy: $raffle->ended->by,
                    endedReason: $raffle->ended->reason,
                    totalTickets: $raffle->totalTickets->toInt(),
                    ticketAmount: $raffle->ticketPrice->amount,
                    ticketCurrency: $raffle->ticketPrice->currency,
                ));
            }
            $this->transactionBoundary->commit();
        } catch (Throwable $exception) {
            $this->transactionBoundary->rollback();

            throw $exception;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\StartRaffle;

use App\Foundation\DomainEventRegistry\Raffle\RaffleStartedV1Event;
use App\Framework\Application\Command\CommandHandlerInterface;
use App\Framework\Domain\Event\DomainEventBusInterface;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use Throwable;

final readonly class StartRaffleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionBoundaryInterface $transactionBoundary,
        private RaffleEventStoreRepository $repository,
        private DomainEventBusInterface $domainEventBus,
    ) {
    }

    public function __invoke(StartRaffleCommand $command): void
    {
        try {
            $raffle = $this->repository->get($command->id);
            $raffle->start($command->started);

            $this->transactionBoundary->begin();
            $this->repository->store($raffle);

            $this->domainEventBus->publish(RaffleStartedV1Event::fromNew(
                id: $raffle->getAggregateId()->toString(),
                name: $raffle->name->toString(),
                prize: $raffle->prize->toString(),
                createdAt: $raffle->created->at,
                createdBy: $raffle->created->by,
                startAt: $raffle->startAt->toDateTime(),
                startedAt: $raffle->started->at, // @phpstan-ignore-line argument.type
                startedBy: $raffle->started->by, // @phpstan-ignore-line argument.type
                closeAt: $raffle->closeAt->toDateTime(),
                drawAt: $raffle->drawAt->toDateTime(),
                totalTickets: $raffle->totalTickets->toInt(),
                ticketAmount: $raffle->ticketPrice->amount,
                ticketCurrency: $raffle->ticketPrice->currency,
            ));
            $this->transactionBoundary->commit();
        } catch (Throwable $exception) {
            $this->transactionBoundary->rollback();

            throw $exception;
        }
    }
}

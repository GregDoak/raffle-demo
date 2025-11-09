<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\CreateRaffle;

use App\Foundation\DomainEventRegistry\Raffle\RaffleCreatedV1Event;
use App\Framework\Application\Command\CommandHandlerInterface;
use App\Framework\Application\Exception\ExceptionTransformerInterface;
use App\Framework\Domain\Event\DomainEventBusInterface;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use Throwable;

final readonly class CreateRaffleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionBoundaryInterface $transactionBoundary,
        private RaffleEventStoreRepository $repository,
        private ExceptionTransformerInterface $exceptionTransformer,
        private DomainEventBusInterface $domainEventBus,
    ) {
    }

    public function __invoke(CreateRaffleCommand $command): void
    {
        try {
            $raffle = Raffle::create(
                $command->id,
                $command->name,
                $command->prize,
                $command->startAt,
                $command->closeAt,
                $command->drawAt,
                $command->totalTickets,
                $command->ticketPrice,
                $command->created,
            );

            $this->transactionBoundary->begin();
            $this->repository->store($raffle);

            $this->domainEventBus->publish(
                RaffleCreatedV1Event::fromNew(
                    id: $raffle->getAggregateId()->toString(),
                    name: $raffle->name->toString(),
                    prize: $raffle->prize->toString(),
                    createdAt: $raffle->created->at,
                    createdBy: $raffle->created->by,
                    startAt: $raffle->startAt->toDateTime(),
                    closeAt: $raffle->closeAt->toDateTime(),
                    drawAt: $raffle->drawAt->toDateTime(),
                    totalTickets: $raffle->totalTickets->toInt(),
                    ticketAmount: $raffle->ticketPrice->amount,
                    ticketCurrency: $raffle->ticketPrice->currency,
                ),
            );
            $this->transactionBoundary->commit();
        } catch (Throwable $exception) {
            $this->transactionBoundary->rollback();

            throw $this->exceptionTransformer->transform($exception);
        }
    }
}

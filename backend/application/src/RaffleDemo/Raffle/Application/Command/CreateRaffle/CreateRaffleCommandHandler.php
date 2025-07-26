<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\CreateRaffle;

use App\Framework\Application\Command\CommandHandlerInterface;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use Throwable;

final readonly class CreateRaffleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionBoundaryInterface $transactionBoundary,
        private RaffleEventStoreRepository $repository,
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
            $this->transactionBoundary->commit();
        } catch (Throwable $exception) {
            $this->transactionBoundary->rollback();

            throw $exception;
        }
    }
}

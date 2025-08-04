<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\StartRaffle;

use App\Framework\Application\Command\CommandHandlerInterface;
use App\Framework\Domain\Exception\AggregateNotFoundException;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use Throwable;

final readonly class StartRaffleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionBoundaryInterface $transactionBoundary,
        private RaffleEventStoreRepository $repository,
    ) {
    }

    public function __invoke(StartRaffleCommand $command): void
    {
        try {
            $raffle = $this->repository->get($command->id);
            if ($raffle === null) {
                throw AggregateNotFoundException::fromAggregateId();
            }

            $raffle->start($command->started);

            $this->transactionBoundary->begin();
            $this->repository->store($raffle);
            $this->transactionBoundary->commit();
        } catch (Throwable $exception) {
            $this->transactionBoundary->rollback();

            throw $exception;
        }
    }
}

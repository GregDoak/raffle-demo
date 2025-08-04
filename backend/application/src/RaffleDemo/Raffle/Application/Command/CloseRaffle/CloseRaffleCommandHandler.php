<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\CloseRaffle;

use App\Framework\Application\Command\CommandHandlerInterface;
use App\Framework\Domain\Exception\AggregateNotFoundException;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use Throwable;

final readonly class CloseRaffleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionBoundaryInterface $transactionBoundary,
        private RaffleEventStoreRepository $repository,
    ) {
    }

    public function __invoke(CloseRaffleCommand $command): void
    {
        try {
            $raffle = $this->repository->get($command->id);
            if ($raffle === null) {
                throw AggregateNotFoundException::fromAggregateId();
            }

            $raffle->close($command->closed);

            $this->transactionBoundary->begin();
            $this->repository->store($raffle);
            $this->transactionBoundary->commit();
        } catch (Throwable $exception) {
            $this->transactionBoundary->rollback();

            throw $exception;
        }
    }
}

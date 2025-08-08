<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant;

use App\Framework\Application\Command\CommandHandlerInterface;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use Throwable;

final readonly class AllocateTicketToParticipantCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionBoundaryInterface $transactionBoundary,
        private RaffleEventStoreRepository $repository,
    ) {
    }

    public function __invoke(AllocateTicketToParticipantCommand $command): void
    {
        try {
            $raffle = $this->repository->get($command->id);
            $raffle->allocateTicketToParticipant($command->ticketAllocation);

            $this->transactionBoundary->begin();
            $this->repository->store($raffle);
            $this->transactionBoundary->commit();
        } catch (Throwable $exception) {
            $this->transactionBoundary->rollback();

            throw $exception;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Context\RaffleDemo\Raffle\Application\Command;

use App\Framework\Domain\Exception\AggregateNotFoundException;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant\AllocateTicketToParticipantCommand;
use App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant\AllocateTicketToParticipantCommandHandler;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommandHandler;
use App\RaffleDemo\Raffle\Application\Command\StartRaffle\StartRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\StartRaffle\StartRaffleCommandHandler;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use App\Tests\Double\Framework\Domain\Repository\TransactionBoundarySpy;
use DateTimeInterface;

final readonly class RaffleApplicationContext
{
    private TransactionBoundaryInterface $transactionBoundary;

    public function __construct(
        private RaffleEventStoreRepository $repository,
    ) {
        $this->transactionBoundary = new TransactionBoundarySpy();
    }

    public function create(?CreateRaffleCommand $command = null): Raffle
    {
        $command = $command ?? $this->getCreateRaffleCommand();

        $handler = new CreateRaffleCommandHandler(
            $this->transactionBoundary,
            $this->repository,
        );

        $handler->__invoke($command);

        return $this->getRaffle($command->id);
    }

    public function start(StartRaffleCommand $command): Raffle
    {
        $handler = new StartRaffleCommandHandler(
            $this->transactionBoundary,
            $this->repository,
        );

        $handler->__invoke($command);

        return $this->getRaffle($command->id);
    }

    public function allocateTicketToParticipant(AllocateTicketToParticipantCommand $command): Raffle
    {
        $handler = new AllocateTicketToParticipantCommandHandler(
            $this->transactionBoundary,
            $this->repository,
        );

        $handler->__invoke($command);

        return $this->getRaffle($command->id);
    }

    /** @param array{amount: int, currency: string} $ticketPrice */
    public function getCreateRaffleCommand(
        string $name = 'raffle-name',
        string $prize = 'raffle-prize',
        string $startAt = '2025-01-02 00:00:00',
        string $closeAt = '2025-01-03 00:00:00',
        string $drawAt = '2025-01-03 00:00:00',
        int $totalTickets = 100,
        array $ticketPrice = ['amount' => 100, 'currency' => 'GBP'],
        string $createdAt = '2025-01-01 00:00:00',
    ): CreateRaffleCommand {
        return CreateRaffleCommand::create(
            $name,
            $prize,
            $startAt,
            $closeAt,
            $drawAt,
            $totalTickets,
            $ticketPrice,
            $createdAt,
        );
    }

    public function getStartRaffleCommand(
        string $id,
        DateTimeInterface $startedAt,
        string $startedBy,
    ): StartRaffleCommand {
        return StartRaffleCommand::create(
            $id,
            $startedAt,
            $startedBy,
        );
    }

    public function getAllocateTicketToParticipantCommand(
        string $id,
        int $ticketAllocatedQuantity,
        string $ticketAllocatedTo,
        DateTimeInterface $ticketAllocatedAt,
    ): AllocateTicketToParticipantCommand {
        return AllocateTicketToParticipantCommand::create(
            $id,
            $ticketAllocatedQuantity,
            $ticketAllocatedTo,
            $ticketAllocatedAt,
        );
    }

    private function getRaffle(RaffleAggregateId $id): Raffle
    {
        return $this->repository->get($id) ?? throw AggregateNotFoundException::fromAggregateId();
    }
}

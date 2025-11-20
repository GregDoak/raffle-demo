<?php

declare(strict_types=1);

namespace App\Tests\Context\RaffleDemo\Raffle\Application\Command;

use App\Framework\Application\Exception\ExceptionTransformer;
use App\Framework\Application\Exception\ExceptionTransformerInterface;
use App\Framework\Domain\Repository\TransactionBoundaryInterface;
use App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant\AllocateTicketToParticipantCommand;
use App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant\AllocateTicketToParticipantCommandHandler;
use App\RaffleDemo\Raffle\Application\Command\CloseRaffle\CloseRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\CloseRaffle\CloseRaffleCommandHandler;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommandHandler;
use App\RaffleDemo\Raffle\Application\Command\DrawPrize\DrawPrizeCommand;
use App\RaffleDemo\Raffle\Application\Command\DrawPrize\DrawPrizeCommandHandler;
use App\RaffleDemo\Raffle\Application\Command\StartRaffle\StartRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\StartRaffle\StartRaffleCommandHandler;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use App\Tests\Double\Framework\Domain\Event\DomainEventBusSpy;
use App\Tests\Double\Framework\Domain\Repository\TransactionBoundarySpy;
use DateTimeInterface;

final readonly class RaffleApplicationContext
{
    private ExceptionTransformerInterface $exceptionTransformer;
    private TransactionBoundaryInterface $transactionBoundary;
    private DomainEventBusSpy $domainEventBus;

    public function __construct(
        private RaffleEventStoreRepository $repository,
    ) {
        $this->exceptionTransformer = new ExceptionTransformer();
        $this->transactionBoundary = new TransactionBoundarySpy();
        $this->domainEventBus = new DomainEventBusSpy();
    }

    public function create(?CreateRaffleCommand $command = null): Raffle
    {
        $command = $command ?? self::getCreateRaffleCommand();

        $handler = new CreateRaffleCommandHandler(
            $this->transactionBoundary,
            $this->repository,
            $this->exceptionTransformer,
            $this->domainEventBus,
        );

        $handler->__invoke($command);

        return $this->repository->get($command->id);
    }

    public function start(StartRaffleCommand $command): Raffle
    {
        $handler = new StartRaffleCommandHandler(
            $this->transactionBoundary,
            $this->repository,
            $this->domainEventBus,
        );

        $handler->__invoke($command);

        return $this->repository->get($command->id);
    }

    public function allocateTicketToParticipant(AllocateTicketToParticipantCommand $command): Raffle
    {
        $handler = new AllocateTicketToParticipantCommandHandler(
            $this->transactionBoundary,
            $this->repository,
        );

        $handler->__invoke($command);

        return $this->repository->get($command->id);
    }

    public function close(CloseRaffleCommand $command): Raffle
    {
        $handler = new CloseRaffleCommandHandler(
            $this->transactionBoundary,
            $this->repository,
            $this->domainEventBus,
        );

        $handler->__invoke($command);

        return $this->repository->get($command->id);
    }

    public function drawPrize(DrawPrizeCommand $command): Raffle
    {
        $handler = new DrawPrizeCommandHandler(
            $this->transactionBoundary,
            $this->repository,
            $this->domainEventBus,
        );

        $handler->__invoke($command);

        return $this->repository->get($command->id);
    }

    /** @param array{amount: int, currency: string} $ticketPrice */
    public static function getCreateRaffleCommand(
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

    public static function getStartRaffleCommand(
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

    public static function getAllocateTicketToParticipantCommand(
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

    public static function getCloseRaffleCommand(
        string $id,
        DateTimeInterface $closedAt,
        string $closedBy,
    ): CloseRaffleCommand {
        return CloseRaffleCommand::create(
            $id,
            $closedAt,
            $closedBy,
        );
    }

    public function getDrawPrizeCommand(
        string $id,
        DateTimeInterface $drawnAt,
        string $drawnBy,
    ): DrawPrizeCommand {
        return DrawPrizeCommand::create(
            $id,
            $drawnAt,
            $drawnBy,
        );
    }
}

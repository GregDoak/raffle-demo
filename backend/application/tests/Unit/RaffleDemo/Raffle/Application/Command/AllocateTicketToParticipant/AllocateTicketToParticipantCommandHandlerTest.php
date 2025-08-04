<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant;

use App\Foundation\Clock\Clock;
use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\Framework\Domain\Exception\AggregateNotFoundException;
use App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant\AllocateTicketToParticipantCommand;
use App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant\AllocateTicketToParticipantCommandHandler;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketAllocationException;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use App\Tests\Context\RaffleDemo\Raffle\Application\Command\RaffleApplicationContext;
use App\Tests\Double\Framework\Domain\Model\Event\AggregateEventsBusSpy;
use App\Tests\Double\Framework\Domain\Repository\InMemoryEventStore;
use App\Tests\Double\Framework\Domain\Repository\TransactionBoundarySpy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

final class AllocateTicketToParticipantCommandHandlerTest extends TestCase
{
    private RaffleApplicationContext $context;
    private AllocateTicketToParticipantCommandHandler $handler;
    private RaffleEventStoreRepository $repository;
    private TransactionBoundarySpy $transactionBoundary;

    protected function setUp(): void
    {
        $this->repository = new RaffleEventStoreRepository(
            new InMemoryEventStore(),
            new AggregateEventsBusSpy(),
        );
        $this->transactionBoundary = new TransactionBoundarySpy();

        $this->context = new RaffleApplicationContext(
            $this->repository,
        );

        $this->handler = new AllocateTicketToParticipantCommandHandler(
            $this->transactionBoundary,
            $this->repository,
        );
    }

    #[Test]
    public function it_allocates_a_ticket_to_a_participant_for_an_existing_raffle(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $raffle = $this->context->create();
        $raffle = $this->context->start(
            $this->context->getStartRaffleCommand(
                id: $raffle->getAggregateId()->toString(),
                startedAt: Clock::fromString('2025-01-02 00:00:01'),
                startedBy: 'system',
            ),
        );
        $command = AllocateTicketToParticipantCommand::create(
            id: $raffle->getAggregateId()->toString(),
            ticketAllocatedQuantity: 20,
            ticketAllocatedTo: 'participant',
            ticketAllocatedAt: Clock::fromString('2025-01-02 01:00:00'),
        );

        // Act
        $this->handler->__invoke($command);

        // Assert
        self::assertTrue($this->transactionBoundary->hasBegun);
        self::assertTrue($this->transactionBoundary->hasCommitted);

        $raffle = $this->repository->get($command->id);
        self::assertNotNull($raffle);
        self::assertTrue($raffle->ticketAllocations->has($command->ticketAllocation));
    }

    #[Test]
    public function it_fails_when_the_raffle_does_not_exist(): void
    {
        // Arrange
        $command = AllocateTicketToParticipantCommand::create(
            id: RaffleAggregateId::fromNew()->toString(),
            ticketAllocatedQuantity: 20,
            ticketAllocatedTo: 'participant',
            ticketAllocatedAt: Clock::fromString('2025-01-02 01:00:00'),
        );

        // Act
        self::expectException(AggregateNotFoundException::class);

        $this->handler->__invoke($command);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_an_error_occurs(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $raffle = $this->context->create();
        $raffle = $this->context->start(
            $this->context->getStartRaffleCommand(
                id: $raffle->getAggregateId()->toString(),
                startedAt: Clock::fromString('2025-01-02 00:00:01'),
                startedBy: 'system',
            ),
        );
        $command = AllocateTicketToParticipantCommand::create(
            id: $raffle->getAggregateId()->toString(),
            ticketAllocatedQuantity: 200000,
            ticketAllocatedTo: 'participant',
            ticketAllocatedAt: Clock::fromString('2025-01-01 00:00:00'),
        );
        $exception = null;

        // Act
        try {
            $this->handler->__invoke($command);
        } catch (Throwable $exception) {
        }

        // Assert
        self::assertFalse($this->transactionBoundary->hasBegun);
        self::assertFalse($this->transactionBoundary->hasCommitted);
        self::assertTrue($this->transactionBoundary->hasRolledBack);
        self::assertInstanceOf(InvalidTicketAllocationException::class, $exception);
    }
}

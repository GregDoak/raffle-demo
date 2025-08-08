<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Command\StartRaffle;

use App\Foundation\Clock\Clock;
use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\Framework\Domain\Exception\AggregateNotFoundException;
use App\RaffleDemo\Raffle\Application\Command\StartRaffle\StartRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\StartRaffle\StartRaffleCommandHandler;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidStartedException;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use App\Tests\Context\RaffleDemo\Raffle\Application\Command\RaffleApplicationContext;
use App\Tests\Double\Framework\Domain\Model\Event\AggregateEventsBusSpy;
use App\Tests\Double\Framework\Domain\Repository\InMemoryEventStore;
use App\Tests\Double\Framework\Domain\Repository\TransactionBoundarySpy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

final class StartRaffleCommandHandlerTest extends TestCase
{
    private RaffleApplicationContext $context;
    private StartRaffleCommandHandler $handler;
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

        $this->handler = new StartRaffleCommandHandler(
            $this->transactionBoundary,
            $this->repository,
        );
    }

    #[Test]
    public function it_starts_an_existing_raffle(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $raffle = $this->context->create();
        $command = StartRaffleCommand::create(
            id: $raffle->getAggregateId()->toString(),
            startedAt: Clock::fromString('2025-01-02 00:00:01'),
            startedBy: 'test-user',
        );

        // Act
        $this->handler->__invoke($command);

        // Assert
        self::assertTrue($this->transactionBoundary->hasBegun);
        self::assertTrue($this->transactionBoundary->hasCommitted);

        $raffle = $this->repository->get($command->id);
        self::assertSame($command->started->toArray(), $raffle->started?->toArray());
    }

    #[Test]
    public function it_fails_when_the_raffle_does_not_exist(): void
    {
        // Arrange
        $command = StartRaffleCommand::create(
            id: RaffleAggregateId::fromNew()->toString(),
            startedAt: Clock::fromString('2025-01-02 00:00:01'),
            startedBy: 'test-user',
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
        $command = StartRaffleCommand::create(
            id: $raffle->getAggregateId()->toString(),
            startedAt: Clock::fromString('2025-01-01 00:00:00'),
            startedBy: 'user',
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
        self::assertInstanceOf(InvalidStartedException::class, $exception);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Command\CreateRaffle;

use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\Framework\Application\Exception\ExceptionTransformer;
use App\Framework\Application\Exception\ValidationException;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommandHandler;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidCreatedException;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use App\Tests\Double\Framework\Domain\Model\Event\AggregateEventsBusSpy;
use App\Tests\Double\Framework\Domain\Repository\InMemoryEventStore;
use App\Tests\Double\Framework\Domain\Repository\TransactionBoundarySpy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

final class CreateRaffleCommandHandlerTest extends TestCase
{
    private CreateRaffleCommandHandler $handler;
    private RaffleEventStoreRepository $repository;

    private TransactionBoundarySpy $transactionBoundary;

    protected function setUp(): void
    {
        $this->repository = new RaffleEventStoreRepository(
            new InMemoryEventStore(),
            new AggregateEventsBusSpy(),
        );
        $this->transactionBoundary = new TransactionBoundarySpy();

        $this->handler = new CreateRaffleCommandHandler(
            $this->transactionBoundary,
            $this->repository,
            new ExceptionTransformer(),
        );
    }

    #[Test]
    public function it_creates_a_raffle(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $command = CreateRaffleCommand::create(
            name: 'raffle-demo',
            prize: 'raffle-prize',
            startAt: '2025-01-02 00:00:00',
            closeAt: '2025-01-03 00:00:00',
            drawAt: '2025-01-03 00:00:00',
            totalTickets: 100,
            ticketPrice: ['amount' => 1000, 'currency' => 'GBP'],
            createdBy: 'user',
        );

        // Act
        $this->handler->__invoke($command);

        // Assert
        self::assertTrue($this->transactionBoundary->hasBegun);
        self::assertTrue($this->transactionBoundary->hasCommitted);
        self::assertFalse($this->transactionBoundary->hasRolledBack);

        $raffle = $this->repository->get($command->id);
        self::assertSame($command->name->toString(), $raffle->name->toString());
        self::assertSame($command->prize->toString(), $raffle->prize->toString());
        self::assertSame($command->startAt->toString(), $raffle->startAt->toString());
        self::assertSame($command->closeAt->toString(), $raffle->closeAt->toString());
        self::assertSame($command->drawAt->toString(), $raffle->drawAt->toString());
        self::assertSame($command->totalTickets->toInt(), $raffle->totalTickets->toInt());
        self::assertSame($command->ticketPrice->toArray(), $raffle->ticketPrice->toArray());
        self::assertSame($command->created->toArray(), $raffle->created->toArray());
    }

    #[Test]
    public function it_fails_when_an_error_occurs(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2026-01-01 00:00:00')); // Created date is after the start at
        $command = CreateRaffleCommand::create(
            name: 'raffle-demo',
            prize: 'raffle-prize',
            startAt: '2025-01-02 00:00:00',
            closeAt: '2025-01-03 00:00:00',
            drawAt: '2025-01-03 00:00:00',
            totalTickets: 100,
            ticketPrice: ['amount' => 1000, 'currency' => 'GBP'],
            createdBy: 'user',
        );
        $exception = null;
        $expectedException = ValidationException::fromError(InvalidCreatedException::fromCreatedAtAfterStartAt()->getMessage());

        // Act
        try {
            $this->handler->__invoke($command);
        } catch (Throwable $exception) {
        }

        // Assert
        self::assertFalse($this->transactionBoundary->hasBegun);
        self::assertFalse($this->transactionBoundary->hasCommitted);
        self::assertTrue($this->transactionBoundary->hasRolledBack);
        self::assertEquals($expectedException, $exception);
    }
}

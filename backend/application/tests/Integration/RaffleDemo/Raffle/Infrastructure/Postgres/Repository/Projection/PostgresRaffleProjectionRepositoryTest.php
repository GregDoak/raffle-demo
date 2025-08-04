<?php

declare(strict_types=1);

namespace App\Tests\Integration\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\Raffle;
use App\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\PostgresRaffleProjectionRepository;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\Test;

final class PostgresRaffleProjectionRepositoryTest extends AbstractIntegrationTestCase
{
    private PostgresRaffleProjectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(PostgresRaffleProjectionRepository::class);
    }

    #[Test]
    public function it_stores_a_new_raffle(): void
    {
        // Arrange
        $originalRecords = self::getRepositoryRecords();
        $raffle = new Raffle(
            id: RaffleAggregateId::fromNew()->toString(),
            name: 'raffle-name',
            prize: 'raffle-prize',
            createdAt: Clock::now(),
            createdBy: 'created_by',
            startAt: Clock::now(),
            startedAt: Clock::now(),
            startedBy: 'started_by',
            totalTickets: 123,
            remainingTickets: 0,
            ticketAmount: 100,
            ticketCurrency: 'GBP',
            closeAt: Clock::now(),
            closedAt: Clock::now(),
            closedBy: 'closed_by',
            drawAt: Clock::now(),
            drawnAt: Clock::now(),
            drawnBy: 'drawn_by',
            winningAllocation: 'winning_allocation',
            winningTicketNumber: 75,
            wonBy: 'won_by',
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffle);

        // Assert
        self::assertCount(0, $originalRecords);
        self::assertCount(1, self::getRepositoryRecords());
    }

    #[Test]
    public function it_updates_a_raffle_when_a_given_id_is_already_stored(): void
    {
        // Arrange
        $originalRecords = self::getRepositoryRecords();
        $id = RaffleAggregateId::fromNew()->toString();
        $raffle = Raffle::fromCreated(
            id: $id,
            name: 'raffle-name',
            prize: 'raffle-prize',
            createdAt: Clock::now(),
            createdBy: 'created_by',
            startAt: Clock::now(),
            totalTickets: 123,
            remainingTickets: 0,
            ticketAmount: 100,
            ticketCurrency: 'GBP',
            closeAt: Clock::now(),
            drawAt: Clock::now(),
            lastOccurredAt: Clock::now(),
        );
        $this->repository->store($raffle);

        // Act
        $raffle = $raffle->started(
            startedAt: Clock::now(),
            startedBy: 'started_by',
            lastOccurredAt: Clock::now(),
        );
        $this->repository->store($raffle);

        // Assert
        self::assertCount(0, $originalRecords);
        $records = self::getRepositoryRecords();
        self::assertCount(1, $records);
        self::assertSame('raffle-name', $records[0]['name'] ?? null);
        self::assertSame('started_by', $records[0]['started_by'] ?? null);
    }

    #[Test]
    public function it_retrieves_a_stored_raffle_by_id(): void
    {
        // Arrange
        $id = RaffleAggregateId::fromNew()->toString();
        $raffle = new Raffle(
            id: $id,
            name: 'raffle-name',
            prize: 'raffle-prize',
            createdAt: Clock::now(),
            createdBy: 'created_by',
            startAt: Clock::now(),
            startedAt: Clock::now(),
            startedBy: 'started_by',
            totalTickets: 123,
            remainingTickets: 0,
            ticketAmount: 100,
            ticketCurrency: 'GBP',
            closeAt: Clock::now(),
            closedAt: Clock::now(),
            closedBy: 'closed_by',
            drawAt: Clock::now(),
            drawnAt: Clock::now(),
            drawnBy: 'drawn_by',
            winningAllocation: 'winning_allocation',
            winningTicketNumber: 75,
            wonBy: 'won_by',
            lastOccurredAt: Clock::now(),
        );
        $this->repository->store($raffle);

        // Act
        $persistedRaffle = $this->repository->getById($id);

        // Assert
        self::assertEquals($raffle, $persistedRaffle);
    }

    #[Test]
    public function it_returns_null_when_retrieving_an_unknown_id(): void
    {
        // Act
        $raffle = $this->repository->getById(RaffleAggregateId::fromNew()->toString());

        // Assert
        self::assertNull($raffle);
    }

    /** @return array<int, mixed[]> */
    private static function getRepositoryRecords(): array
    {
        return self::getContainer()
            ->get(Connection::class)
            ->executeQuery('SELECT * FROM raffle.projection_raffle')
            ->fetchAllAssociative();
    }
}

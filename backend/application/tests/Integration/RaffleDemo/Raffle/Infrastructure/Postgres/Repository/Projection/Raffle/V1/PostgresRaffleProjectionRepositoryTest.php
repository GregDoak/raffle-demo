<?php

declare(strict_types=1);

namespace App\Tests\Integration\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\Raffle\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleQuery;
use App\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\Raffle\V1\PostgresRaffleProjectionRepository;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $originalRecords = $this->repository->query(new RaffleQuery());
        $raffle = new Raffle(
            id: RaffleAggregateId::fromNew()->toString(),
            name: 'raffle-name',
            prize: 'raffle-prize',
            status: 'ended',
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
            endedAt: Clock::now(),
            endedBy: 'ended_by',
            endedReason: 'reason',
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffle);

        // Assert
        $raffles = $this->repository->query(new RaffleQuery());
        self::assertCount(0, $originalRecords);
        self::assertCount(1, $raffles);
        self::assertEquals($raffle, $raffles[0]);
    }

    #[Test]
    public function it_updates_a_raffle_when_a_given_id_is_already_stored(): void
    {
        // Arrange
        $originalRecords = $this->repository->query(new RaffleQuery());
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
        $records = $this->repository->query(new RaffleQuery());
        self::assertCount(1, $records);
        self::assertSame('raffle-name', $records[0]->name ?? null);
        self::assertSame('started_by', $records[0]->startedBy ?? null);
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
            status: 'ended',
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
            endedAt: Clock::now(),
            endedBy: 'ended_by',
            endedReason: 'reason',
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

    #[Test, DataProvider('it_can_query_data_provider')]
    public function it_can_query(RaffleQuery $query, int $expectedResults): void
    {
        // Arrange
        $raffle = new Raffle(
            id: RaffleAggregateId::fromNew()->toString(),
            name: 'raffle-name',
            prize: 'raffle-prize',
            status: 'ended',
            createdAt: Clock::fromString('2025-01-01 00:00:00'),
            createdBy: 'created_by',
            startAt: Clock::fromString('2025-01-02 00:00:00'),
            startedAt: Clock::fromString('2025-01-02 00:00:01'),
            startedBy: 'started_by',
            totalTickets: 123,
            remainingTickets: 0,
            ticketAmount: 100,
            ticketCurrency: 'GBP',
            closeAt: Clock::fromString('2025-01-03 00:00:00'),
            closedAt: Clock::fromString('2025-01-03 00:00:01'),
            closedBy: 'closed_by',
            drawAt: Clock::fromString('2025-01-04 00:00:00'),
            drawnAt: Clock::fromString('2025-01-04 00:00:01'),
            drawnBy: 'drawn_by',
            winningAllocation: 'winning_allocation',
            winningTicketNumber: 75,
            wonBy: 'won_by',
            endedAt: Clock::fromString('2025-01-05 00:00:00'),
            endedBy: 'ended_by',
            endedReason: 'reason',
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffle);

        // Assert
        $raffles = $this->repository->query($query);
        self::assertCount($expectedResults, $raffles);
    }

    public static function it_can_query_data_provider(): Generator
    {
        yield 'fuzzy result with name' => ['query' => new RaffleQuery()->withName('name'), 'expectedResults' => 1];
        yield 'no result with name' => ['query' => new RaffleQuery()->withName('INVALID'), 'expectedResults' => 0];
        yield 'fuzzy result with prize' => ['query' => new RaffleQuery()->withPrize('prize'), 'expectedResults' => 1];
        yield 'no result with prize' => ['query' => new RaffleQuery()->withPrize('INVALID'), 'expectedResults' => 0];
        yield 'exact result with status' => ['query' => new RaffleQuery()->withStatus('ended'), 'expectedResults' => 1];
        yield 'no result with status' => ['query' => new RaffleQuery()->withStatus('INVALID'), 'expectedResults' => 0];
        yield 'no result with less than start at date' => ['query' => new RaffleQuery()->withStartAt(Clock::fromString('2025-01-01 00:00:00')), 'expectedResults' => 0];
        yield 'result with equal start at date' => ['query' => new RaffleQuery()->withStartAt(Clock::fromString('2025-01-02 00:00:00')), 'expectedResults' => 1];
        yield 'result with greater than start at date' => ['query' => new RaffleQuery()->withStartAt(Clock::fromString('2025-01-03 00:00:00')), 'expectedResults' => 1];
        yield 'no result with less than close at date' => ['query' => new RaffleQuery()->withCloseAt(Clock::fromString('2025-01-02 00:00:00')), 'expectedResults' => 0];
        yield 'result with equal close at date' => ['query' => new RaffleQuery()->withCloseAt(Clock::fromString('2025-01-03 00:00:00')), 'expectedResults' => 1];
        yield 'result with greater than close at date' => ['query' => new RaffleQuery()->withCloseAt(Clock::fromString('2025-01-04 00:00:00')), 'expectedResults' => 1];
        yield 'no result with less than draw at date' => ['query' => new RaffleQuery()->withDrawAt(Clock::fromString('2025-01-03 00:00:00')), 'expectedResults' => 0];
        yield 'result with equal draw at date' => ['query' => new RaffleQuery()->withDrawAt(Clock::fromString('2025-01-04 00:00:00')), 'expectedResults' => 1];
        yield 'result with greater than draw at date' => ['query' => new RaffleQuery()->withDrawAt(Clock::fromString('2025-01-05 00:00:00')), 'expectedResults' => 1];
    }

    #[Test, DataProvider('it_can_query_with_a_given_sort_order_data_provider')]
    public function it_can_query_with_a_given_sort_order(RaffleQuery $query, string $expectedFirstId): void
    {
        // Arrange
        $raffle1 = new Raffle(
            id: '9f6c7589-b103-4066-84e8-bf9c4539ec66',
            name: 'raffle-name-1',
            prize: 'raffle-prize-1',
            status: 'ended',
            createdAt: Clock::fromString('2025-01-01 00:00:00'),
            createdBy: 'created_by',
            startAt: Clock::fromString('2025-01-02 00:00:00'),
            startedAt: Clock::fromString('2025-01-02 00:00:01'),
            startedBy: 'started_by',
            totalTickets: 123,
            remainingTickets: 0,
            ticketAmount: 100,
            ticketCurrency: 'GBP',
            closeAt: Clock::fromString('2025-01-03 00:00:00'),
            closedAt: Clock::fromString('2025-01-03 00:00:01'),
            closedBy: 'closed_by',
            drawAt: Clock::fromString('2025-01-04 00:00:00'),
            drawnAt: Clock::fromString('2025-01-04 00:00:01'),
            drawnBy: 'drawn_by',
            winningAllocation: 'winning_allocation',
            winningTicketNumber: 75,
            wonBy: 'won_by',
            endedAt: Clock::fromString('2025-01-05 00:00:00'),
            endedBy: 'ended_by',
            endedReason: 'reason',
            lastOccurredAt: Clock::now(),
        );

        $raffle2 = new Raffle(
            id: '392101e5-af30-477c-a3b1-d78a25a3da45',
            name: 'raffle-name-2',
            prize: 'raffle-prize-2',
            status: 'drawn',
            createdAt: Clock::fromString('2025-02-01 00:00:00'),
            createdBy: 'created_by',
            startAt: Clock::fromString('2025-02-02 00:00:00'),
            startedAt: Clock::fromString('2025-02-02 00:00:01'),
            startedBy: 'started_by',
            totalTickets: 123,
            remainingTickets: 0,
            ticketAmount: 100,
            ticketCurrency: 'GBP',
            closeAt: Clock::fromString('2025-02-03 00:00:00'),
            closedAt: Clock::fromString('2025-02-03 00:00:01'),
            closedBy: 'closed_by',
            drawAt: Clock::fromString('2025-02-04 00:00:00'),
            drawnAt: Clock::fromString('2025-02-04 00:00:01'),
            drawnBy: 'drawn_by',
            winningAllocation: 'winning_allocation',
            winningTicketNumber: 75,
            wonBy: 'won_by',
            endedAt: null,
            endedBy: null,
            endedReason: null,
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffle1);
        $this->repository->store($raffle2);

        // Assert
        $raffles = $this->repository->query($query);
        self::assertSame($expectedFirstId, $raffles[0]->id);
    }

    public static function it_can_query_with_a_given_sort_order_data_provider(): Generator
    {
        yield 'sort by name ascending' => ['query' => new RaffleQuery()->sortBy('name'), 'expectedFirstId' => '9f6c7589-b103-4066-84e8-bf9c4539ec66'];
        yield 'sort by name descending' => ['query' => new RaffleQuery()->sortBy('name', 'DESC'), 'expectedFirstId' => '392101e5-af30-477c-a3b1-d78a25a3da45'];
        yield 'sort by prize ascending' => ['query' => new RaffleQuery()->sortBy('prize'), 'expectedFirstId' => '9f6c7589-b103-4066-84e8-bf9c4539ec66'];
        yield 'sort by prize descending' => ['query' => new RaffleQuery()->sortBy('prize', 'DESC'), 'expectedFirstId' => '392101e5-af30-477c-a3b1-d78a25a3da45'];
        yield 'sort by status ascending' => ['query' => new RaffleQuery()->sortBy('status'), 'expectedFirstId' => '392101e5-af30-477c-a3b1-d78a25a3da45'];
        yield 'sort by status descending' => ['query' => new RaffleQuery()->sortBy('status', 'DESC'), 'expectedFirstId' => '9f6c7589-b103-4066-84e8-bf9c4539ec66'];
        yield 'sort by start at ascending' => ['query' => new RaffleQuery()->sortBy('startAt'), 'expectedFirstId' => '9f6c7589-b103-4066-84e8-bf9c4539ec66'];
        yield 'sort by start at descending' => ['query' => new RaffleQuery()->sortBy('startAt', 'DESC'), 'expectedFirstId' => '392101e5-af30-477c-a3b1-d78a25a3da45'];
        yield 'sort by close at ascending' => ['query' => new RaffleQuery()->sortBy('closeAt'), 'expectedFirstId' => '9f6c7589-b103-4066-84e8-bf9c4539ec66'];
        yield 'sort by close at descending' => ['query' => new RaffleQuery()->sortBy('closeAt', 'DESC'), 'expectedFirstId' => '392101e5-af30-477c-a3b1-d78a25a3da45'];
        yield 'sort by draw at ascending' => ['query' => new RaffleQuery()->sortBy('drawAt'), 'expectedFirstId' => '9f6c7589-b103-4066-84e8-bf9c4539ec66'];
        yield 'sort by draw at descending' => ['query' => new RaffleQuery()->sortBy('drawAt', 'DESC'), 'expectedFirstId' => '392101e5-af30-477c-a3b1-d78a25a3da45'];
    }

    #[Test, DataProvider('it_can_query_with_a_given_pagination_data_provider')]
    public function it_can_query_with_a_given_pagination(RaffleQuery $query, string $expectedFirstId): void
    {
        // Arrange
        $raffle1 = new Raffle(
            id: '9f6c7589-b103-4066-84e8-bf9c4539ec66',
            name: 'raffle-name-1',
            prize: 'raffle-prize-1',
            status: 'ended',
            createdAt: Clock::fromString('2025-01-01 00:00:00'),
            createdBy: 'created_by',
            startAt: Clock::fromString('2025-01-02 00:00:00'),
            startedAt: Clock::fromString('2025-01-02 00:00:01'),
            startedBy: 'started_by',
            totalTickets: 123,
            remainingTickets: 0,
            ticketAmount: 100,
            ticketCurrency: 'GBP',
            closeAt: Clock::fromString('2025-01-03 00:00:00'),
            closedAt: Clock::fromString('2025-01-03 00:00:01'),
            closedBy: 'closed_by',
            drawAt: Clock::fromString('2025-01-04 00:00:00'),
            drawnAt: Clock::fromString('2025-01-04 00:00:01'),
            drawnBy: 'drawn_by',
            winningAllocation: 'winning_allocation',
            winningTicketNumber: 75,
            wonBy: 'won_by',
            endedAt: Clock::fromString('2025-01-05 00:00:00'),
            endedBy: 'ended_by',
            endedReason: 'reason',
            lastOccurredAt: Clock::now(),
        );

        $raffle2 = new Raffle(
            id: '392101e5-af30-477c-a3b1-d78a25a3da45',
            name: 'raffle-name-2',
            prize: 'raffle-prize-2',
            status: 'drawn',
            createdAt: Clock::fromString('2025-02-01 00:00:00'),
            createdBy: 'created_by',
            startAt: Clock::fromString('2025-02-02 00:00:00'),
            startedAt: Clock::fromString('2025-02-02 00:00:01'),
            startedBy: 'started_by',
            totalTickets: 123,
            remainingTickets: 0,
            ticketAmount: 100,
            ticketCurrency: 'GBP',
            closeAt: Clock::fromString('2025-02-03 00:00:00'),
            closedAt: Clock::fromString('2025-02-03 00:00:01'),
            closedBy: 'closed_by',
            drawAt: Clock::fromString('2025-02-04 00:00:00'),
            drawnAt: Clock::fromString('2025-02-04 00:00:01'),
            drawnBy: 'drawn_by',
            winningAllocation: 'winning_allocation',
            winningTicketNumber: 75,
            wonBy: 'won_by',
            endedAt: null,
            endedBy: null,
            endedReason: null,
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffle1);
        $this->repository->store($raffle2);

        // Assert
        $raffles = $this->repository->query($query);
        self::assertSame($expectedFirstId, $raffles[0]->id);
    }

    public static function it_can_query_with_a_given_pagination_data_provider(): Generator
    {
        yield 'paginate first record' => ['query' => new RaffleQuery()->paginate(1), 'expectedFirstId' => '9f6c7589-b103-4066-84e8-bf9c4539ec66'];
        yield 'paginate second record' => ['query' => new RaffleQuery()->paginate(1, 1), 'expectedFirstId' => '392101e5-af30-477c-a3b1-d78a25a3da45'];
    }
}

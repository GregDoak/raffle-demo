<?php

declare(strict_types=1);

namespace App\Tests\Integration\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\RaffleWinner\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinner;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinnerQuery;
use App\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\RaffleWinner\V1\PostgresRaffleWinnerProjectionRepository;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

final class PostgresRaffleWinnerProjectionRepositoryTest extends AbstractIntegrationTestCase
{
    private PostgresRaffleWinnerProjectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(PostgresRaffleWinnerProjectionRepository::class);
    }

    #[Test]
    public function it_stores_a_new_raffle_allocation(): void
    {
        // Arrange
        $originalRecords = $this->repository->query(new RaffleWinnerQuery());
        $raffleWinner = new RaffleWinner(
            raffleId: RaffleAggregateId::fromNew()->toString(),
            raffleAllocationHash: 'hash',
            drawnAt: Clock::now(),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffleWinner);

        // Assert
        $raffleWinners = $this->repository->query(new RaffleWinnerQuery());
        self::assertCount(0, $originalRecords);
        self::assertCount(1, $raffleWinners);
        self::assertEquals($raffleWinner, $raffleWinners[0]);
    }

    #[Test]
    public function it_fails_to_store_a_record_with_a_duplicate_raffle_id(): void
    {
        // Arrange
        $raffleId = RaffleAggregateId::fromNew()->toString();
        $raffleWinner1 = new RaffleWinner(
            raffleId: $raffleId,
            raffleAllocationHash: 'hash-1',
            drawnAt: Clock::now(),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );

        $raffleWinner2 = new RaffleWinner(
            raffleId: $raffleId,
            raffleAllocationHash: 'hash-2',
            drawnAt: Clock::now(),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );
        $this->repository->store($raffleWinner1);
        $exception = null;

        //  Act
        try {
            $this->repository->store($raffleWinner2);
        } catch (Throwable $exception) {
        }

        // Assert
        self::assertNotNull($exception);
    }

    #[Test]
    public function it_fails_to_store_a_record_with_a_duplicate_raffle_allocation_hash(): void
    {
        // Arrange
        $hash = 'duplicate-hash';
        $raffleWinner1 = new RaffleWinner(
            raffleId: RaffleAggregateId::fromNew()->toString(),
            raffleAllocationHash: $hash,
            drawnAt: Clock::now(),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );

        $raffleWinner2 = new RaffleWinner(
            raffleId: RaffleAggregateId::fromNew()->toString(),
            raffleAllocationHash: $hash,
            drawnAt: Clock::now(),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );
        $this->repository->store($raffleWinner1);
        $exception = null;

        //  Act
        try {
            $this->repository->store($raffleWinner2);
        } catch (Throwable $exception) {
        }

        // Assert
        self::assertNotNull($exception);
    }

    #[Test, DataProvider('it_can_query_data_provider')]
    public function it_can_query(RaffleWinnerQuery $query, int $expectedResults): void
    {
        // Arrange
        $raffleWinner = new RaffleWinner(
            raffleId: '1f4c8ef9-c780-4eb7-92d1-3b26c94c990d',
            raffleAllocationHash: 'hash',
            drawnAt: Clock::fromString('2025-01-04 00:00:00'),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffleWinner);

        // Assert
        $raffleWinners = $this->repository->query($query);
        self::assertCount($expectedResults, $raffleWinners);
    }

    public static function it_can_query_data_provider(): Generator
    {
        yield 'exact result with raffle id' => ['query' => new RaffleWinnerQuery()->withRaffleId('1f4c8ef9-c780-4eb7-92d1-3b26c94c990d'), 'expectedResults' => 1];
        yield 'no result with raffle id' => ['query' => new RaffleWinnerQuery()->withRaffleId('8c11042b-1318-435b-8605-528a7dda9319'), 'expectedResults' => 0];
        yield 'no result with less than drawn at date' => ['query' => new RaffleWinnerQuery()->withDrawnAt(Clock::fromString('2025-01-03 00:00:00')), 'expectedResults' => 0];
        yield 'result with equal drawn at date' => ['query' => new RaffleWinnerQuery()->withDrawnAt(Clock::fromString('2025-01-04 00:00:00')), 'expectedResults' => 1];
        yield 'result with greater than drawn at date' => ['query' => new RaffleWinnerQuery()->withDrawnAt(Clock::fromString('2025-01-05 00:00:00')), 'expectedResults' => 1];
    }

    #[Test, DataProvider('it_can_query_with_a_given_sort_order_data_provider')]
    public function it_can_query_with_a_given_sort_order(RaffleWinnerQuery $query, string $expectedFirstId): void
    {
        // Arrange
        $raffleWinner1 = new RaffleWinner(
            raffleId: 'e040d968-e85b-4d64-9260-27a492c8ecea',
            raffleAllocationHash: 'hash-1',
            drawnAt: Clock::fromString('2025-01-01 00:00:00'),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );

        $raffleWinner2 = new RaffleWinner(
            raffleId: 'e3b4c8c0-6286-4409-a902-ff60674aa131',
            raffleAllocationHash: 'hash-2',
            drawnAt: Clock::fromString('2025-01-02 00:00:00'),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffleWinner1);
        $this->repository->store($raffleWinner2);

        // Assert
        $raffleAllocations = $this->repository->query($query);
        self::assertSame($expectedFirstId, $raffleAllocations[0]->raffleId);
    }

    public static function it_can_query_with_a_given_sort_order_data_provider(): Generator
    {
        yield 'sort by raffle id ascending' => ['query' => new RaffleWinnerQuery()->sortBy('raffleId'), 'expectedFirstId' => 'e040d968-e85b-4d64-9260-27a492c8ecea'];
        yield 'sort by raffle id descending' => ['query' => new RaffleWinnerQuery()->sortBy('raffleId', 'DESC'), 'expectedFirstId' => 'e3b4c8c0-6286-4409-a902-ff60674aa131'];
        yield 'sort by drawn at ascending' => ['query' => new RaffleWinnerQuery()->sortBy('drawnAt'), 'expectedFirstId' => 'e040d968-e85b-4d64-9260-27a492c8ecea'];
        yield 'sort by drawn at descending' => ['query' => new RaffleWinnerQuery()->sortBy('drawnAt', 'DESC'), 'expectedFirstId' => 'e3b4c8c0-6286-4409-a902-ff60674aa131'];
    }

    #[Test, DataProvider('it_can_query_with_a_given_pagination_data_provider')]
    public function it_can_query_with_a_given_pagination(RaffleWinnerQuery $query, string $expectedFirstId): void
    {
        // Arrange
        $raffleWinner1 = new RaffleWinner(
            raffleId: 'e040d968-e85b-4d64-9260-27a492c8ecea',
            raffleAllocationHash: 'hash-1',
            drawnAt: Clock::fromString('2025-01-01 00:00:00'),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );

        $raffleWinner2 = new RaffleWinner(
            raffleId: 'e3b4c8c0-6286-4409-a902-ff60674aa131',
            raffleAllocationHash: 'hash-2',
            drawnAt: Clock::fromString('2025-01-02 00:00:00'),
            winningTicketNumber: 10,
            winner: 'winner',
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffleWinner1);
        $this->repository->store($raffleWinner2);

        // Assert
        $raffleAllocations = $this->repository->query($query);
        self::assertSame($expectedFirstId, $raffleAllocations[0]->raffleId);
    }

    public static function it_can_query_with_a_given_pagination_data_provider(): Generator
    {
        yield 'paginate first record' => ['query' => new RaffleWinnerQuery()->paginate(1), 'expectedFirstId' => 'e040d968-e85b-4d64-9260-27a492c8ecea'];
        yield 'paginate second record' => ['query' => new RaffleWinnerQuery()->paginate(1, 1), 'expectedFirstId' => 'e3b4c8c0-6286-4409-a902-ff60674aa131'];
    }
}

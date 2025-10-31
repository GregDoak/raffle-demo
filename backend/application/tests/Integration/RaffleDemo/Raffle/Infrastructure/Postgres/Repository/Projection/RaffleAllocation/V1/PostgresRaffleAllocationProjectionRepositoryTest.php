<?php

declare(strict_types=1);

namespace App\Tests\Integration\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\RaffleAllocation\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocation;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationQuery;
use App\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\Projection\RaffleAllocation\V1\PostgresRaffleAllocationProjectionRepository;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

final class PostgresRaffleAllocationProjectionRepositoryTest extends AbstractIntegrationTestCase
{
    private PostgresRaffleAllocationProjectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(PostgresRaffleAllocationProjectionRepository::class);
    }

    #[Test]
    public function it_stores_a_new_raffle_allocation(): void
    {
        // Arrange
        $originalRecords = $this->repository->query(new RaffleAllocationQuery());
        $raffleAllocation = new RaffleAllocation(
            raffleId: RaffleAggregateId::fromNew()->toString(),
            hash: 'hash',
            allocatedAt: Clock::now(),
            allocatedTo: 'allocatedTo',
            quantity: 5,
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffleAllocation);

        // Assert
        $raffleAllocations = $this->repository->query(new RaffleAllocationQuery());
        self::assertCount(0, $originalRecords);
        self::assertCount(1, $raffleAllocations);
        self::assertEquals($raffleAllocation, $raffleAllocations[0]);
    }

    #[Test]
    public function it_fails_to_store_a_record_with_a_duplicate_hash(): void
    {
        // Arrange
        $raffleAllocation = new RaffleAllocation(
            raffleId: RaffleAggregateId::fromNew()->toString(),
            hash: 'hash',
            allocatedAt: Clock::now(),
            allocatedTo: 'allocatedTo',
            quantity: 5,
            lastOccurredAt: Clock::now(),
        );
        $this->repository->store($raffleAllocation);
        $exception = null;

        //  Act
        try {
            $this->repository->store($raffleAllocation);
        } catch (Throwable $exception) {
        }

        // Assert
        self::assertNotNull($exception);
    }

    #[Test, DataProvider('it_can_query_data_provider')]
    public function it_can_query(RaffleAllocationQuery $query, int $expectedResults): void
    {
        // Arrange
        $raffleAllocation = new RaffleAllocation(
            raffleId: 'b6e795d4-fd02-40ce-bbf8-f001ef87ed70',
            hash: 'hash',
            allocatedAt: Clock::now(),
            allocatedTo: 'allocatedTo',
            quantity: 5,
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffleAllocation);

        // Assert
        $raffleAllocations = $this->repository->query($query);
        self::assertCount($expectedResults, $raffleAllocations);
    }

    public static function it_can_query_data_provider(): Generator
    {
        yield 'exact result with raffle id' => ['query' => new RaffleAllocationQuery()->withRaffleId('b6e795d4-fd02-40ce-bbf8-f001ef87ed70'), 'expectedResults' => 1];
        yield 'no result with raffle id' => ['query' => new RaffleAllocationQuery()->withRaffleId('8c11042b-1318-435b-8605-528a7dda9319'), 'expectedResults' => 0];
    }

    #[Test, DataProvider('it_can_query_with_a_given_sort_order_data_provider')]
    public function it_can_query_with_a_given_sort_order(RaffleAllocationQuery $query, string $expectedFirstId): void
    {
        // Arrange
        $raffleAllocation1 = new RaffleAllocation(
            raffleId: 'b6e795d4-fd02-40ce-bbf8-f001ef87ed70',
            hash: 'hash-1',
            allocatedAt: Clock::now(),
            allocatedTo: 'allocatedTo-1',
            quantity: 5,
            lastOccurredAt: Clock::now(),
        );

        $raffleAllocation2 = new RaffleAllocation(
            raffleId: '50c9519f-64b6-4de1-9a36-e9dd44e7d878',
            hash: 'hash-2',
            allocatedAt: Clock::now(),
            allocatedTo: 'allocatedTo-1',
            quantity: 5,
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffleAllocation1);
        $this->repository->store($raffleAllocation2);

        // Assert
        $raffleAllocations = $this->repository->query($query);
        self::assertSame($expectedFirstId, $raffleAllocations[0]->raffleId);
    }

    public static function it_can_query_with_a_given_sort_order_data_provider(): Generator
    {
        yield 'sort by raffle id ascending' => ['query' => new RaffleAllocationQuery()->sortBy('raffleId'), 'expectedFirstId' => '50c9519f-64b6-4de1-9a36-e9dd44e7d878'];
        yield 'sort by raffle id descending' => ['query' => new RaffleAllocationQuery()->sortBy('raffleId', 'DESC'), 'expectedFirstId' => 'b6e795d4-fd02-40ce-bbf8-f001ef87ed70'];
    }

    #[Test, DataProvider('it_can_query_with_a_given_pagination_data_provider')]
    public function it_can_query_with_a_given_pagination(RaffleAllocationQuery $query, string $expectedFirstId): void
    {
        // Arrange
        // Arrange
        $raffleAllocation1 = new RaffleAllocation(
            raffleId: '50c9519f-64b6-4de1-9a36-e9dd44e7d878',
            hash: 'hash-1',
            allocatedAt: Clock::now(),
            allocatedTo: 'allocatedTo-1',
            quantity: 5,
            lastOccurredAt: Clock::now(),
        );

        $raffleAllocation2 = new RaffleAllocation(
            raffleId: 'b6e795d4-fd02-40ce-bbf8-f001ef87ed70',
            hash: 'hash-2',
            allocatedAt: Clock::now(),
            allocatedTo: 'allocatedTo-1',
            quantity: 5,
            lastOccurredAt: Clock::now(),
        );

        //  Act
        $this->repository->store($raffleAllocation1);
        $this->repository->store($raffleAllocation2);

        // Assert
        $raffleAllocations = $this->repository->query($query);
        self::assertSame($expectedFirstId, $raffleAllocations[0]->raffleId);
    }

    public static function it_can_query_with_a_given_pagination_data_provider(): Generator
    {
        yield 'paginate first record' => ['query' => new RaffleAllocationQuery()->paginate(1), 'expectedFirstId' => '50c9519f-64b6-4de1-9a36-e9dd44e7d878'];
        yield 'paginate second record' => ['query' => new RaffleAllocationQuery()->paginate(1, 1), 'expectedFirstId' => 'b6e795d4-fd02-40ce-bbf8-f001ef87ed70'];
    }
}

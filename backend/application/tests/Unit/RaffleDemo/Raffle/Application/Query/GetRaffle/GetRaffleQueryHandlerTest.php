<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Query\GetRaffle;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Application\Query\GetRaffle\GetRaffleQuery;
use App\RaffleDemo\Raffle\Application\Query\GetRaffle\GetRaffleQueryHandler;
use App\RaffleDemo\Raffle\Application\Query\GetRaffle\GetRaffleResult;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocation;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationProjectionRepositoryInterface;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionDomainContext;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\Raffle\V1\InMemoryRaffleProjectionRepository;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\RaffleAllocation\V1\InMemoryRaffleAllocationProjectionRepository;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetRaffleQueryHandlerTest extends TestCase
{
    private RaffleAllocationProjectionRepositoryInterface $raffleAllocationProjectionRepository;
    private RaffleProjectionRepositoryInterface $raffleProjectionRepository;

    private GetRaffleQueryHandler $handler;

    protected function setUp(): void
    {
        $this->raffleAllocationProjectionRepository = new InMemoryRaffleAllocationProjectionRepository();
        $this->raffleProjectionRepository = new InMemoryRaffleProjectionRepository();

        $this->handler = new GetRaffleQueryHandler(
            raffleProjectionRepository: $this->raffleProjectionRepository,
            raffleAllocationProjectionRepository: $this->raffleAllocationProjectionRepository,
        );
    }

    /**
     * @param Raffle[]           $raffles
     * @param RaffleAllocation[] $allocations
     */
    #[Test, DataProvider('it_returns_the_expected_result_for_given_raffles_provider')]
    public function it_returns_the_expected_result_for_given_raffles(
        array $raffles,
        array $allocations,
        GetRaffleQuery $query,
        GetRaffleResult $expectedResult,
    ): void {
        // Arrange
        foreach ($raffles as $raffle) {
            $this->raffleProjectionRepository->store($raffle);
        }

        foreach ($allocations as $allocation) {
            $this->raffleAllocationProjectionRepository->store($allocation);
        }

        // Act
        $result = $this->handler->__invoke($query);

        // Assert
        self::assertEquals($expectedResult, $result);
    }

    public static function it_returns_the_expected_result_for_given_raffles_provider(): Generator
    {
        yield 'null result' => [
            'raffles' => [],
            'allocations' => [],
            'query' => GetRaffleQuery::create(id: 'id-1'),
            'expectedResult' => GetRaffleResult::fromNull(),
        ];

        yield 'raffle result with no allocations' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'created',
                    startAt: $startAt = Clock::fromString(
                        '2025-01-01 00:00:00',
                    ),
                ),
            ],
            'allocations' => [],
            'query' => GetRaffleQuery::create(id: 'id-1'),
            'expectedResult' => GetRaffleResult::fromRaffle(
                RaffleProjectionDomainContext::create(id: 'id-1', status: 'created', startAt: $startAt),
            ),
        ];

        yield 'raffle result with allocations' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'created',
                    startAt: $startAt = Clock::fromString(
                        '2025-01-01 00:00:01',
                    ),
                ),
                RaffleProjectionDomainContext::create(
                    id: 'id-2',
                    status: 'created',
                    startAt: Clock::fromString(
                        '2025-01-01 00:00:02',
                    ),
                ),
                RaffleProjectionDomainContext::create(
                    id: 'id-3',
                    status: 'created',
                    startAt: Clock::fromString(
                        '2025-01-01 00:00:03',
                    ),
                ),
            ],
            'allocations' => $allocations = [
                new RaffleAllocation(raffleId: 'id-1', hash: 'hash-1', allocatedAt: Clock::now(), allocatedTo: 'allocatedTo-1', quantity: 1, lastOccurredAt: Clock::now()),
                new RaffleAllocation(raffleId: 'id-2', hash: 'hash-2', allocatedAt: Clock::now(), allocatedTo: 'allocatedTo-2', quantity: 2, lastOccurredAt: Clock::now()),
                new RaffleAllocation(raffleId: 'id-3', hash: 'hash-3', allocatedAt: Clock::now(), allocatedTo: 'allocatedTo-3', quantity: 3, lastOccurredAt: Clock::now()),
                new RaffleAllocation(raffleId: 'id-1', hash: 'hash-4', allocatedAt: Clock::now(), allocatedTo: 'allocatedTo-1', quantity: 4, lastOccurredAt: Clock::now()),
            ],
            'query' => GetRaffleQuery::create(id: 'id-1'),
            'expectedResult' => GetRaffleResult::fromRaffle(
                RaffleProjectionDomainContext::create(id: 'id-1', status: 'created', startAt: $startAt),
                $allocations[0],
                $allocations[3],
            ),
        ];
    }
}

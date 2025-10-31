<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Query\GetRaffles;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Application\Query\GetRaffles\GetRafflesQuery;
use App\RaffleDemo\Raffle\Application\Query\GetRaffles\GetRafflesQueryHandler;
use App\RaffleDemo\Raffle\Application\Query\GetRaffles\GetRafflesResult;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionDomainContext;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\Raffle\V1\InMemoryRaffleProjectionRepository;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetRafflesQueryHandlerTest extends TestCase
{
    private RaffleProjectionRepositoryInterface $repository;

    private GetRafflesQueryHandler $handler;

    protected function setUp(): void
    {
        $this->repository = new InMemoryRaffleProjectionRepository();

        $this->handler = new GetRafflesQueryHandler(
            repository: $this->repository,
        );
    }

    /** @param Raffle[] $raffles */
    #[Test, DataProvider('it_returns_the_expected_result_for_given_raffles_provider')]
    public function it_returns_the_expected_result_for_given_raffles(
        array $raffles,
        GetRafflesQuery $query,
        GetRafflesResult $expectedResult,
    ): void {
        // Arrange
        foreach ($raffles as $raffle) {
            $this->repository->store($raffle);
        }

        // Act
        $result = $this->handler->__invoke($query);

        // Assert
        self::assertEquals($expectedResult, $result);
    }

    public static function it_returns_the_expected_result_for_given_raffles_provider(): Generator
    {
        yield 'empty results' => [
            'raffles' => [],
            'query' => GetRafflesQuery::create(
                name: null,
                prize: null,
                status: null,
                limit: 1,
                offset: 0,
                sortField: 'startAt',
                sortOrder: 'ASC',
            ),
            'expectedResult' => GetRafflesResult::fromRaffles(0),
        ];

        yield 'single raffle result' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'created',
                    startAt: $startAt = Clock::fromString(
                        '2025-01-01 00:00:00',
                    ),
                ),
            ],
            'query' => GetRafflesQuery::create(
                name: null,
                prize: null,
                status: null,
                limit: 1,
                offset: 0,
                sortField: 'startAt',
                sortOrder: 'ASC',
            ),
            'expectedResult' => GetRafflesResult::fromRaffles(
                1,
                RaffleProjectionDomainContext::create(id: 'id-1', status: 'created', startAt: $startAt),
            ),
        ];

        yield 'multiple paginated results' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'created',
                    startAt: Clock::fromString(
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
            'query' => GetRafflesQuery::create(
                name: null,
                prize: null,
                status: null,
                limit: 1,
                offset: 2,
                sortField: 'startAt',
                sortOrder: 'ASC',
            ),
            'expectedResult' => GetRafflesResult::fromRaffles(
                3,
                RaffleProjectionDomainContext::create(
                    id: 'id-3',
                    status: 'created',
                    startAt: Clock::fromString(
                        '2025-01-01 00:00:03',
                    ),
                ),
            ),
        ];
    }
}

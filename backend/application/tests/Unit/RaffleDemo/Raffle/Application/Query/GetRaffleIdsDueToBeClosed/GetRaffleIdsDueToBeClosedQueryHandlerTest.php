<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed\GetRaffleIdsDueToBeClosedQuery;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed\GetRaffleIdsDueToBeClosedQueryHandler;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed\GetRaffleIdsDueToBeClosedResult;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionDomainContext;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\Raffle\V1\InMemoryRaffleProjectionRepository;
use DateTimeInterface;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetRaffleIdsDueToBeClosedQueryHandlerTest extends TestCase
{
    private RaffleProjectionRepositoryInterface $repository;

    private GetRaffleIdsDueToBeClosedQueryHandler $handler;

    protected function setUp(): void
    {
        $this->repository = new InMemoryRaffleProjectionRepository();

        $this->handler = new GetRaffleIdsDueToBeClosedQueryHandler(
            repository: $this->repository,
        );
    }

    /** @param Raffle[] $raffles */
    #[Test, DataProvider('it_returns_the_expected_result_for_given_raffles_provider')]
    public function it_returns_the_expected_result_for_given_raffles(
        array $raffles,
        DateTimeInterface $closeAt,
        GetRaffleIdsDueToBeClosedResult $expectedResult,
    ): void {
        // Arrange
        foreach ($raffles as $raffle) {
            $this->repository->store($raffle);
        }

        // Act
        $result = $this->handler->__invoke(GetRaffleIdsDueToBeClosedQuery::create($closeAt));

        // Assert
        self::assertEquals($expectedResult, $result);
    }

    public static function it_returns_the_expected_result_for_given_raffles_provider(): Generator
    {
        yield 'no raffles to be closed' => [
            'raffles' => [],
            'closeAt' => Clock::now(),
            'expectedResult' => GetRaffleIdsDueToBeClosedResult::fromRaffles(),
        ];

        yield 'single raffle to be closed' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'started',
                    closeAt: $closeAt = Clock::fromString(
                        '2025-01-01 00:00:00',
                    ),
                ),
            ],
            'closeAt' => $closeAt,
            'expectedResult' => GetRaffleIdsDueToBeClosedResult::fromRaffles(
                RaffleProjectionDomainContext::create(id: 'id-1', status: 'started', closeAt: $closeAt),
            ),
        ];

        yield 'multiple raffles to be closed' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'started',
                    closeAt: $closeAt = Clock::fromString(
                        '2025-01-01 00:00:00',
                    ),
                ),
                RaffleProjectionDomainContext::create(id: 'id-2', status: 'started', closeAt: $closeAt),
                RaffleProjectionDomainContext::create(id: 'id-3', status: 'started', closeAt: $closeAt),
            ],
            'closeAt' => $closeAt,
            'expectedResult' => GetRaffleIdsDueToBeClosedResult::fromRaffles(
                RaffleProjectionDomainContext::create(id: 'id-1', status: 'started', closeAt: $closeAt),
                RaffleProjectionDomainContext::create(id: 'id-2', status: 'started', closeAt: $closeAt),
                RaffleProjectionDomainContext::create(id: 'id-3', status: 'started', closeAt: $closeAt),
            ),
        ];
    }
}

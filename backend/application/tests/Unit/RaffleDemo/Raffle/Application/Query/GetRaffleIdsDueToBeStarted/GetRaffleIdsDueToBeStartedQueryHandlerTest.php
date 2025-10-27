<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeStarted;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeStarted\GetRaffleIdsDueToBeStartedQuery;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeStarted\GetRaffleIdsDueToBeStartedQueryHandler;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeStarted\GetRaffleIdsDueToBeStartedResult;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionDomainContext;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\Raffle\V1\InMemoryRaffleProjectionRepository;
use DateTimeInterface;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetRaffleIdsDueToBeStartedQueryHandlerTest extends TestCase
{
    private RaffleProjectionRepositoryInterface $repository;

    private GetRaffleIdsDueToBeStartedQueryHandler $handler;

    protected function setUp(): void
    {
        $this->repository = new InMemoryRaffleProjectionRepository();

        $this->handler = new GetRaffleIdsDueToBeStartedQueryHandler(
            repository: $this->repository,
        );
    }

    /** @param Raffle[] $raffles */
    #[Test, DataProvider('it_returns_the_expected_result_for_given_raffles_provider')]
    public function it_returns_the_expected_result_for_given_raffles(
        array $raffles,
        DateTimeInterface $startAt,
        GetRaffleIdsDueToBeStartedResult $expectedResult,
    ): void {
        // Arrange
        foreach ($raffles as $raffle) {
            $this->repository->store($raffle);
        }

        // Act
        $result = $this->handler->__invoke(GetRaffleIdsDueToBeStartedQuery::create($startAt));

        // Assert
        self::assertEquals($expectedResult, $result);
    }

    public static function it_returns_the_expected_result_for_given_raffles_provider(): Generator
    {
        yield 'no raffles to be started' => [
            'raffles' => [],
            'startAt' => Clock::now(),
            'expectedResult' => GetRaffleIdsDueToBeStartedResult::fromRaffles(),
        ];

        yield 'single raffle to be started' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'created',
                    startAt: $startAt = Clock::fromString(
                        '2025-01-01 00:00:00',
                    ),
                ),
            ],
            'startAt' => $startAt,
            'expectedResult' => GetRaffleIdsDueToBeStartedResult::fromRaffles(
                RaffleProjectionDomainContext::create(id: 'id-1', status: 'created', startAt: $startAt),
            ),
        ];

        yield 'multiple raffles to be started' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'created',
                    startAt: $startAt = Clock::fromString(
                        '2025-01-01 00:00:00',
                    ),
                ),
                RaffleProjectionDomainContext::create(id: 'id-2', status: 'created', startAt: $startAt),
                RaffleProjectionDomainContext::create(id: 'id-3', status: 'created', startAt: $startAt),
            ],
            'startAt' => $startAt,
            'expectedResult' => GetRaffleIdsDueToBeStartedResult::fromRaffles(
                RaffleProjectionDomainContext::create(id: 'id-1', status: 'created', startAt: $startAt),
                RaffleProjectionDomainContext::create(id: 'id-2', status: 'created', startAt: $startAt),
                RaffleProjectionDomainContext::create(id: 'id-3', status: 'created', startAt: $startAt),
            ),
        ];
    }
}

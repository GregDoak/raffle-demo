<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn\GetRaffleIdsDueToBeDrawnQuery;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn\GetRaffleIdsDueToBeDrawnQueryHandler;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn\GetRaffleIdsDueToBeDrawnResult;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionDomainContext;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\Raffle\V1\InMemoryRaffleProjectionRepository;
use DateTimeInterface;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetRaffleIdsDueToBeDrawnQueryHandlerTest extends TestCase
{
    private RaffleProjectionRepositoryInterface $repository;

    private GetRaffleIdsDueToBeDrawnQueryHandler $handler;

    protected function setUp(): void
    {
        $this->repository = new InMemoryRaffleProjectionRepository();

        $this->handler = new GetRaffleIdsDueToBeDrawnQueryHandler(
            repository: $this->repository,
        );
    }

    /** @param Raffle[] $raffles */
    #[Test, DataProvider('it_returns_the_expected_result_for_given_raffles_provider')]
    public function it_returns_the_expected_result_for_given_raffles(
        array $raffles,
        DateTimeInterface $drawAt,
        GetRaffleIdsDueToBeDrawnResult $expectedResult,
    ): void {
        // Arrange
        foreach ($raffles as $raffle) {
            $this->repository->store($raffle);
        }

        // Act
        $result = $this->handler->__invoke(GetRaffleIdsDueToBeDrawnQuery::create($drawAt));

        // Assert
        self::assertEquals($expectedResult, $result);
    }

    public static function it_returns_the_expected_result_for_given_raffles_provider(): Generator
    {
        yield 'no raffles to be drawn' => [
            'raffles' => [],
            'drawAt' => Clock::now(),
            'expectedResult' => GetRaffleIdsDueToBeDrawnResult::fromRaffles(),
        ];

        yield 'single raffle to be drawn' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'closed',
                    drawAt: $drawAt = Clock::fromString(
                        '2025-01-01 00:00:00',
                    ),
                ),
            ],
            'drawAt' => $drawAt,
            'expectedResult' => GetRaffleIdsDueToBeDrawnResult::fromRaffles(
                RaffleProjectionDomainContext::create(id: 'id-1', status: 'closed', drawAt: $drawAt),
            ),
        ];

        yield 'multiple raffles to be drawn' => [
            'raffles' => [
                RaffleProjectionDomainContext::create(
                    id: 'id-1',
                    status: 'closed',
                    drawAt: $drawAt = Clock::fromString(
                        '2025-01-01 00:00:00',
                    ),
                ),
                RaffleProjectionDomainContext::create(id: 'id-2', status: 'closed', drawAt: $drawAt),
                RaffleProjectionDomainContext::create(id: 'id-3', status: 'closed', drawAt: $drawAt),
            ],
            'drawAt' => $drawAt,
            'expectedResult' => GetRaffleIdsDueToBeDrawnResult::fromRaffles(
                RaffleProjectionDomainContext::create(id: 'id-1', status: 'closed', drawAt: $drawAt),
                RaffleProjectionDomainContext::create(id: 'id-2', status: 'closed', drawAt: $drawAt),
                RaffleProjectionDomainContext::create(id: 'id-3', status: 'closed', drawAt: $drawAt),
            ),
        ];
    }
}

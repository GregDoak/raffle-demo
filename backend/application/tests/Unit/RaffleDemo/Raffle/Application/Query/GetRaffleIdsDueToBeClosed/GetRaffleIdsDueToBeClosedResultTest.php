<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed;

use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed\GetRaffleIdsDueToBeClosedResult;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeStarted\GetRaffleIdsDueToBeStartedResult;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjectionDomainContext;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetRaffleIdsDueToBeClosedResultTest extends TestCase
{
    #[Test]
    public function it_does_not_allow_duplicate_ids(): void
    {
        // Arrange
        $raffles = [
            RaffleProjectionDomainContext::create(id: 'id-1'),
            RaffleProjectionDomainContext::create(id: 'id-1'),
            RaffleProjectionDomainContext::create(id: 'id-2'),
        ];

        // Act
        $results = GetRaffleIdsDueToBeClosedResult::fromRaffles(...$raffles);

        // Assert
        self::assertCount(2, $results->getIds());
    }

    #[Test]
    public function it_returns_the_expected_number_of_ids(): void
    {
        // Arrange
        $raffles = [
            RaffleProjectionDomainContext::create(id: 'id-1'),
            RaffleProjectionDomainContext::create(id: 'id-2'),
            RaffleProjectionDomainContext::create(id: 'id-3'),
        ];

        // Act
        $results = GetRaffleIdsDueToBeStartedResult::fromRaffles(...$raffles);

        // Assert
        self::assertCount(3, $results->getIds());
    }
}

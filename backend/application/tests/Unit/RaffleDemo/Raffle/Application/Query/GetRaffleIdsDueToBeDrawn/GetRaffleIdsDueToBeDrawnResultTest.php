<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn;

use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn\GetRaffleIdsDueToBeDrawnResult;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjectionDomainContext;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetRaffleIdsDueToBeDrawnResultTest extends TestCase
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
        $results = GetRaffleIdsDueToBeDrawnResult::fromRaffles(...$raffles);

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
        $results = GetRaffleIdsDueToBeDrawnResult::fromRaffles(...$raffles);

        // Assert
        self::assertCount(3, $results->getIds());
    }
}

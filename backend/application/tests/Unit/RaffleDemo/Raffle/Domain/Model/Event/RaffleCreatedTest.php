<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Model\Event;

use App\Foundation\Clock\Clock;
use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\RaffleDemo\Raffle\Domain\Model\Event\RaffleCreated;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateVersion;
use App\RaffleDemo\Raffle\Domain\ValueObject\CloseAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Created;
use App\RaffleDemo\Raffle\Domain\ValueObject\DrawAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Name;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Prize;
use App\RaffleDemo\Raffle\Domain\ValueObject\StartAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketPrice;
use App\RaffleDemo\Raffle\Domain\ValueObject\TotalTickets;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleCreatedTest extends TestCase
{
    #[Test]
    public function it_can_serialize_and_deserialize_successfully(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 12:13:14'));
        $raffleCreated = new RaffleCreated(
            RaffleAggregateVersion::fromNew(),
            RaffleAggregateId::fromNew(),
            Name::fromString('raffle-demo'),
            Prize::fromString('raffle-prize'),
            StartAt::fromNow(),
            CloseAt::fromString('2025-01-03 00:00:00'),
            DrawAt::fromString('2025-01-03 00:00:00'),
            TotalTickets::fromInt(100),
            TicketPrice::from(amount: 500, currency: 'GBP'),
            Created::from(by: 'user', at: Clock::now()),
            OccurredAt::fromNow(),
        );

        // Act
        $serialized = $raffleCreated->serialize();
        $deserializedRaffleCreated = RaffleCreated::deserialize($serialized);

        // Assert
        self::assertEquals($raffleCreated, $deserializedRaffleCreated);
    }
}

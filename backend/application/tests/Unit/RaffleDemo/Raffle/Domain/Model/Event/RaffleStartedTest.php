<?php

declare(strict_types=1);

namespace App\Tests\RaffleDemo\Raffle\Domain\Model\Event;

use App\Foundation\Clock\Clock;
use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\RaffleDemo\Raffle\Domain\Model\Event\RaffleStarted;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateVersion;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Started;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleStartedTest extends TestCase
{
    #[Test]
    public function it_can_serialize_and_deserialize_successfully(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 12:13:14'));
        $raffleStarted = new RaffleStarted(
            RaffleAggregateVersion::fromNew(),
            RaffleAggregateId::fromNew(),
            Started::from(by: 'system', at: Clock::now()),
            OccurredAt::fromNow(),
        );

        // Act
        $serialized = $raffleStarted->serialize();
        $deserializedRaffleStarted = RaffleStarted::deserialize($serialized);

        // Assert
        self::assertEquals($raffleStarted, $deserializedRaffleStarted);
    }
}

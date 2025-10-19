<?php

declare(strict_types=1);

namespace App\Tests\RaffleDemo\Raffle\Domain\Model\Event;

use App\Foundation\Clock\Clock;
use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\RaffleDemo\Raffle\Domain\Model\Event\RaffleEnded;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateVersion;
use App\RaffleDemo\Raffle\Domain\ValueObject\Ended;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleEndedTest extends TestCase
{
    #[Test]
    public function it_can_serialize_and_deserialize_successfully(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 12:13:14'));
        $raffleEnded = new RaffleEnded(
            RaffleAggregateVersion::fromNew(),
            RaffleAggregateId::fromNew(),
            Ended::from(by: 'user', at: Clock::now(), reason: 'some reason'),
            OccurredAt::fromNow(),
        );

        // Act
        $serialized = $raffleEnded->serialize();
        $deserializedRaffleEnded = RaffleEnded::deserialize($serialized);

        // Assert
        self::assertEquals($raffleEnded, $deserializedRaffleEnded);
    }
}

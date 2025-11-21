<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Model\Event;

use App\Foundation\Clock\Clock;
use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\RaffleDemo\Raffle\Domain\Model\Event\RaffleClosed;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateVersion;
use App\RaffleDemo\Raffle\Domain\ValueObject\Closed;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleClosedTest extends TestCase
{
    #[Test]
    public function it_can_serialize_and_deserialize_successfully(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 12:13:14'));
        $raffleClosed = new RaffleClosed(
            RaffleAggregateVersion::fromNew(),
            RaffleAggregateId::fromNew(),
            Closed::from(by: 'user', at: Clock::now()),
            OccurredAt::fromNow(),
        );

        // Act
        $serialized = $raffleClosed->serialize();
        $deserializedRaffleClosed = RaffleClosed::deserialize($serialized);

        // Assert
        self::assertEquals($raffleClosed, $deserializedRaffleClosed);
    }
}

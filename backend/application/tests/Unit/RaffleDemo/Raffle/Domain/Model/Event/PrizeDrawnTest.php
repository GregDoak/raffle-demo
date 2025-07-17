<?php

declare(strict_types=1);

namespace App\Tests\RaffleDemo\Raffle\Domain\Model\Event;

use App\Foundation\Clock\Clock;
use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\RaffleDemo\Raffle\Domain\Model\Event\PrizeDrawn;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateVersion;
use App\RaffleDemo\Raffle\Domain\ValueObject\Drawn;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use App\RaffleDemo\Raffle\Domain\ValueObject\Winner;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PrizeDrawnTest extends TestCase
{
    #[Test]
    public function it_can_serialize_and_deserialize_successfully(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 12:13:14'));
        $prizeDrawn = new PrizeDrawn(
            RaffleAggregateVersion::fromNew(),
            RaffleAggregateId::fromNew(),
            Drawn::from(by: 'system', at: Clock::now()),
            Winner::fromWinningTicketAllocation(
                ticketAllocation: TicketAllocation::from(
                    quantity: 1,
                    allocatedTo: 'participant',
                    allocatedAt: Clock::now(),
                ),
                ticketNumber: 1,
            ),
            OccurredAt::fromNow(),
        );

        // Act
        $serialized = $prizeDrawn->serialize();
        $deserializedPrizeDrawn = PrizeDrawn::deserialize($serialized);

        // Assert
        self::assertEquals($prizeDrawn, $deserializedPrizeDrawn);
    }
}

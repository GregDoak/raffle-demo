<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Model\Event;

use App\Foundation\Clock\Clock;
use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\RaffleDemo\Raffle\Domain\Model\Event\TicketAllocatedToParticipant;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateVersion;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TicketAllocatedToParticipantTest extends TestCase
{
    #[Test]
    public function it_can_serialize_and_deserialize_successfully(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 12:13:14'));
        $ticketAllocatedToParticipant = new TicketAllocatedToParticipant(
            RaffleAggregateVersion::fromNew(),
            RaffleAggregateId::fromNew(),
            TicketAllocation::from(
                quantity: 1,
                allocatedTo: 'participant',
                allocatedAt: Clock::now(),
            ),
            OccurredAt::fromNow(),
        );

        // Act
        $serialized = $ticketAllocatedToParticipant->serialize();
        $deserializedTicketAllocatedToParticipant = TicketAllocatedToParticipant::deserialize($serialized);

        // Assert
        self::assertEquals($ticketAllocatedToParticipant, $deserializedTicketAllocatedToParticipant);
    }
}

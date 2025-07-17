<?php

declare(strict_types=1);

namespace App\Tests\RaffleDemo\Raffle\Domain\Model;

use App\Foundation\Clock\Clock;
use App\Framework\Domain\Exception\AggregateEventNotHandledException;
use App\Framework\Domain\Model\Event\AggregateEventInterface;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidClosedAtException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidClosedException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidCreatedException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidDrawnException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidStartAtException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidStartedException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketAllocationException;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\ValueObject\CloseAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Closed;
use App\RaffleDemo\Raffle\Domain\ValueObject\Created;
use App\RaffleDemo\Raffle\Domain\ValueObject\DrawAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Drawn;
use App\RaffleDemo\Raffle\Domain\ValueObject\Name;
use App\RaffleDemo\Raffle\Domain\ValueObject\Prize;
use App\RaffleDemo\Raffle\Domain\ValueObject\StartAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Started;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketPrice;
use App\RaffleDemo\Raffle\Domain\ValueObject\TotalTickets;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Model\RaffleDomainContext;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleTest extends TestCase
{
    #[Test]
    public function it_creates_a_raffle(): void
    {
        // Arrange: set values to expected defaults
        $id = RaffleAggregateId::fromNew();
        $name = Name::fromString('raffle-name');
        $prize = Prize::fromString('raffle-prize');
        $startAt = StartAt::fromString('2025-01-02 00:00:00');
        $closeAt = CloseAt::fromString('2025-01-03 00:00:00');
        $drawAt = DrawAt::fromString('2025-01-04 00:00:00');
        $totalTickets = TotalTickets::fromInt(100);
        $ticketPrice = TicketPrice::from(amount: 100, currency: 'GBP');
        $created = Created::from(by: 'test-user', at: Clock::fromString('2025-01-01 00:00:00'));

        // Act: create a raffle
        $raffle = Raffle::create(
            id: $id,
            name: $name,
            prize: $prize,
            startAt: $startAt,
            closeAt: $closeAt,
            drawAt: $drawAt,
            totalTickets: $totalTickets,
            ticketPrice: $ticketPrice,
            created: $created,
        );

        // Assert: values are the same as the original values
        self::assertSame($id, $raffle->getAggregateId());
        self::assertSame($name, $raffle->name);
        self::assertSame($prize, $raffle->prize);
        self::assertSame($startAt, $raffle->startAt);
        self::assertSame($closeAt, $raffle->closeAt);
        self::assertSame($drawAt, $raffle->drawAt);
        self::assertSame($totalTickets, $raffle->totalTickets);
        self::assertSame($ticketPrice, $raffle->ticketPrice);
        self::assertSame($created, $raffle->created);
    }

    #[Test]
    public function it_fails_to_create_a_raffle_when_the_created_at_date_is_after_the_start_at_date(): void
    {
        // Arrange
        $created = Created::from(by: 'test-user', at: Clock::fromString('2025-01-02 00:00:00'));
        $startAt = StartAt::fromString('2025-01-01 00:00:00');

        // Act
        self::expectExceptionMessage(InvalidCreatedException::fromCreatedAtAfterStartAt()->getMessage());

        RaffleDomainContext::create(
            startAt: $startAt,
            created: $created,
        );

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_create_a_raffle_when_the_start_at_date_is_equal_to_the_close_at_date(
    ): void {
        // Arrange
        $startAt = StartAt::fromString('2025-01-01 00:00:00');
        $closeAt = CloseAt::fromString('2025-01-01 00:00:00');

        // Act
        self::expectExceptionMessage(InvalidStartAtException::fromStartAtLessThan1DayBeforeCloseAt()->getMessage());

        RaffleDomainContext::create(
            startAt: $startAt,
            closeAt: $closeAt,
        );

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_create_a_raffle_when_the_start_at_date_is_after_to_the_close_at_date(
    ): void {
        // Arrange
        $startAt = StartAt::fromString('2025-01-02 00:00:00');
        $closeAt = CloseAt::fromString('2025-01-01 00:00:00');

        // Act
        self::expectExceptionMessage(InvalidStartAtException::fromStartAtLessThan1DayBeforeCloseAt()->getMessage());

        RaffleDomainContext::create(
            startAt: $startAt,
            closeAt: $closeAt,
        );

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_create_a_raffle_when_the_start_at_date_is_less_than_1_day_before_the_close_at_date(
    ): void {
        // Arrange
        $startAt = StartAt::fromString('2025-01-01 00:00:00');
        $closeAt = CloseAt::fromString('2025-01-01 23:59:59');

        // Act
        self::expectExceptionMessage(InvalidStartAtException::fromStartAtLessThan1DayBeforeCloseAt()->getMessage());

        RaffleDomainContext::create(
            startAt: $startAt,
            closeAt: $closeAt,
        );

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_create_a_raffle_when_the_close_at_date_is_after_the_draw_at_date(): void
    {
        // Arrange
        $closeAt = CloseAt::fromString('2025-01-04 00:00:00');
        $drawAt = DrawAt::fromString('2025-01-03 00:00:00');

        // Act
        self::expectExceptionMessage(InvalidClosedAtException::fromCloseAtAfterDrawAt()->getMessage());

        RaffleDomainContext::create(
            closeAt: $closeAt,
            drawAt: $drawAt,
        );

        // Assert
        self::fail();
    }

    #[Test]
    public function it_can_start_a_raffle(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $started = Started::from(by: 'test-user', at: Clock::fromString('2025-01-02 00:00:00'));

        // Act
        $raffle->start($started);

        // Assert
        self::assertSame($started, $raffle->started);
    }

    #[Test]
    public function it_fails_to_start_a_raffle_when_the_started_at_date_is_before_the_start_at_date(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create(
            startAt: StartAt::fromString('2025-01-01 01:01:01'),
        );
        $started = Started::from(by: 'test-user', at: Clock::fromString('2025-01-01 00:00:00'));

        // Act
        self::expectExceptionMessage(InvalidStartedException::fromCannotStartBeforeStartAtDate()->getMessage());

        $raffle->start($started);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_start_a_raffle_when_the_raffle_is_already_started(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $started = Started::from(by: 'test-user', at: Clock::fromString('2025-01-02 00:00:01'));

        // Act
        self::expectExceptionMessage(InvalidStartedException::fromAlreadyStarted()->getMessage());

        $raffle->start($started);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_can_allocate_tickets_to_a_raffle(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create(
            totalTickets: TotalTickets::fromInt(1),
        );
        $raffle = RaffleDomainContext::start($raffle);
        $ticketAllocation = TicketAllocation::from(
            quantity: 1,
            allocatedTo: 'test-participant',
            allocatedAt: Clock::fromString('2025-01-02 00:00:02'),
        );

        // Act
        $raffle->allocateTicketToParticipant($ticketAllocation);

        // Assert
        self::assertTrue($raffle->ticketAllocations->has($ticketAllocation));
        self::assertSame(1, $raffle->ticketAllocations->numberOfTicketsAllocated);
    }

    #[Test]
    public function it_fails_to_allocate_tickets_to_a_raffle_when_the_raffle_is_not_started(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $ticketAllocation = TicketAllocation::from(
            quantity: 1,
            allocatedTo: 'test-participant',
            allocatedAt: Clock::fromString('2025-01-02 00:00:02'),
        );

        // Act
        self::expectExceptionMessage(InvalidTicketAllocationException::fromCannotAllocateBeforeStarted()->getMessage());

        $raffle->allocateTicketToParticipant($ticketAllocation);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_allocate_tickets_to_a_raffle_when_the_allocated_at_is_before_the_started_at_date(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $ticketAllocation = TicketAllocation::from(
            quantity: 1,
            allocatedTo: 'test-participant',
            allocatedAt: Clock::fromString('2025-01-01 00:00:00'),
        );

        // Act
        self::expectExceptionMessage(InvalidTicketAllocationException::fromCannotAllocateBeforeStarted()->getMessage());

        $raffle->allocateTicketToParticipant($ticketAllocation);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_allocate_tickets_to_a_raffle_when_the_raffle_is_closed(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::close($raffle);
        $ticketAllocation = TicketAllocation::from(
            quantity: 1,
            allocatedTo: 'test-participant',
            allocatedAt: Clock::fromString('2025-01-02 00:00:01'),
        );

        // Act
        self::expectExceptionMessage(InvalidTicketAllocationException::fromCannotAllocateAfterClosed()->getMessage());

        $raffle->allocateTicketToParticipant($ticketAllocation);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_allocate_tickets_to_a_raffle_when_handling_a_duplicate_ticket(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $ticketAllocation = TicketAllocation::from(
            quantity: 1,
            allocatedTo: 'test-participant',
            allocatedAt: Clock::fromString('2025-01-02 00:00:01'),
        );
        $raffle->allocateTicketToParticipant($ticketAllocation);

        // Act
        self::expectExceptionMessage(InvalidTicketAllocationException::fromDuplicateTicketAllocation()->getMessage());

        $raffle->allocateTicketToParticipant($ticketAllocation);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_allocate_tickets_to_a_raffle_when_the_allocated_amount_exceeds_the_limit(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create(
            totalTickets: TotalTickets::fromInt(1),
        );
        $raffle = RaffleDomainContext::start($raffle);
        $ticketAllocation = TicketAllocation::from(
            quantity: 2,
            allocatedTo: 'test-participant',
            allocatedAt: Clock::fromString('2025-01-02 00:00:01'),
        );

        // Act
        self::expectExceptionMessage(InvalidTicketAllocationException::fromOverAllocationOfTickets()->getMessage());

        $raffle->allocateTicketToParticipant($ticketAllocation);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_can_close_a_raffle(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $closed = Closed::from(by: 'test-user', at: Clock::fromString('2025-01-03 00:00:00'));

        // Act
        $raffle->close($closed);

        // Assert
        self::assertSame($closed, $raffle->closed);
    }

    #[Test]
    public function it_fails_to_close_a_raffle_when_the_raffle_is_already_closed(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $closed = Closed::from(by: 'test-user', at: Clock::fromString('2025-01-03 00:00:00'));
        $raffle->close($closed);

        // Act
        self::expectExceptionMessage(InvalidClosedException::fromAlreadyClosed()->getMessage());

        $raffle->close($closed);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_can_draw_a_raffle(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $ticketAllocation = TicketAllocation::from(
            quantity: 1,
            allocatedTo: 'test-participant',
            allocatedAt: Clock::fromString('2025-01-02 00:00:01'),
        );
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle, $ticketAllocation);
        $raffle = RaffleDomainContext::close($raffle);
        $drawn = Drawn::from(by: 'test-user', at: Clock::fromString('2025-01-03 00:00:00'));

        // Act
        $raffle->drawPrize($drawn);

        // Assert
        self::assertSame($drawn, $raffle->drawn);
        self::assertSame($ticketAllocation, $raffle->winner?->toTicketAllocation());
    }

    #[Test]
    public function it_fails_to_draw_a_raffle_when_the_raffle_is_not_closed(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $drawn = Drawn::from(by: 'test-user', at: Clock::fromString('2025-01-03 00:00:00'));

        // Act
        self::expectExceptionMessage(InvalidDrawnException::fromCannotDrawPrizeBeforeClosed()->getMessage());

        $raffle->drawPrize($drawn);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_draw_a_raffle_when_the_raffle_is_already_drawn(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle);
        $raffle = RaffleDomainContext::close($raffle);
        $drawn = Drawn::from(by: 'test-user', at: Clock::fromString('2025-01-03 00:00:00'));
        $raffle->drawPrize($drawn);

        // Act
        self::expectExceptionMessage(InvalidDrawnException::fromAlreadyDrawn()->getMessage());

        $raffle->drawPrize($drawn);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_to_draw_a_raffle_when_the_raffle_has_no_ticket_allocations(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::close($raffle);
        $drawn = Drawn::from(by: 'test-user', at: Clock::fromString('2025-01-03 00:00:00'));

        // Act
        self::expectExceptionMessage(InvalidDrawnException::fromCannotDrawWhenNoTicketAllocations()->getMessage());

        $raffle->drawPrize($drawn);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_can_draw_a_raffle_using_default_values(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle);
        $raffle = RaffleDomainContext::close($raffle);

        // Act
        $raffle = RaffleDomainContext::drawPrize($raffle);

        // Assert
        self::assertNotNull($raffle->started);
        self::assertSame(1, $raffle->ticketAllocations->numberOfTicketsAllocated);
        self::assertNotNull($raffle->closed);
        self::assertNotNull($raffle->drawn);
        self::assertNotNull($raffle->winner);
    }

    #[Test]
    public function it_cannot_apply_an_unknown_event(): void
    {
        // Arrange
        $event = self::createStub(AggregateEventInterface::class);
        $raffle = RaffleDomainContext::create();

        // Act
        self::expectException(AggregateEventNotHandledException::class);

        $raffle->apply($event);

        // Assert
        self::fail();
    }
}

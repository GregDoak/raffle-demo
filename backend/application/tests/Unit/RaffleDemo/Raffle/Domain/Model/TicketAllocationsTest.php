<?php

declare(strict_types=1);

namespace App\Tests\RaffleDemo\Raffle\Domain\Model;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketAllocationsException;
use App\RaffleDemo\Raffle\Domain\Model\TicketAllocations;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TicketAllocationsTest extends TestCase
{
    /** @param TicketAllocation[] $ticketAllocationCandidates */
    #[Test, DataProvider('it_can_record_a_ticket_allocation_and_track_the_number_of_ticket_allocations_provider')]
    public function it_can_record_a_ticket_allocation_and_track_the_number_of_ticket_allocations(
        array $ticketAllocationCandidates,
        int $expectedNumberOfTicketAllocations,
    ): void {
        // Arrange
        $ticketAllocations = TicketAllocations::fromNew();

        // Act
        foreach ($ticketAllocationCandidates as $ticketAllocation) {
            $ticketAllocations->addTicketAllocation($ticketAllocation);
        }

        // Assert
        self::assertSame($expectedNumberOfTicketAllocations, $ticketAllocations->numberOfTicketsAllocated);
    }

    #[Test]
    public function it_verifies_a_given_ticket_allocation_already_exists(): void
    {
        // Arrange
        $ticketAllocations = TicketAllocations::fromNew();
        $ticketAllocation = TicketAllocation::from(
            quantity: 1,
            allocatedTo: 'user',
            allocatedAt: Clock::now(),
        );
        $ticketAllocations->addTicketAllocation($ticketAllocation);

        // Act
        $result = $ticketAllocations->has($ticketAllocation);

        // Assert
        self::assertTrue($result);
    }

    #[Test]
    public function it_can_draw_a_ticket_allocation_from_given_ticket_number(): void
    {
        // Arrange
        $ticketAllocations = TicketAllocations::fromNew();
        $ticketAllocation = TicketAllocation::from(
            quantity: 10,
            allocatedTo: 'user',
            allocatedAt: Clock::now(),
        );
        $ticketAllocations->addTicketAllocation($ticketAllocation);

        // Act
        $drawnTicketAllocation = $ticketAllocations->drawTicketAllocationFromTicketNumber(5);

        // Assert
        self::assertSame($ticketAllocation, $drawnTicketAllocation);
    }

    #[Test]
    public function it_fails_to_draw_a_ticket_allocation_from_non_existing_ticket_number(): void
    {
        // Arrange
        $ticketAllocations = TicketAllocations::fromNew();
        $ticketAllocation = TicketAllocation::from(
            quantity: 5,
            allocatedTo: 'user',
            allocatedAt: Clock::now(),
        );
        $ticketAllocations->addTicketAllocation($ticketAllocation);

        // Act
        self::expectExceptionMessage(
            InvalidTicketAllocationsException::fromCannotDrawUnallocatedTicket()->getMessage(),
        );
        $ticketAllocations->drawTicketAllocationFromTicketNumber(100);

        // Assert
        self::fail();
    }

    public static function it_can_record_a_ticket_allocation_and_track_the_number_of_ticket_allocations_provider(
    ): Generator {
        yield 'single ticket with single quantity' => [
            'ticketAllocationCandidates' => [
                TicketAllocation::from(
                    quantity: 1,
                    allocatedTo: 'user',
                    allocatedAt: Clock::now(),
                ),
            ],
            'expectedNumberOfTicketAllocations' => 1,
        ];

        yield 'single ticket with multiple quantity' => [
            'ticketAllocationCandidates' => [
                TicketAllocation::from(
                    quantity: 10,
                    allocatedTo: 'user',
                    allocatedAt: Clock::now(),
                ),
            ],
            'expectedNumberOfTicketAllocations' => 10,
        ];

        yield 'multiple tickets with single quantity' => [
            'ticketAllocationCandidates' => [
                TicketAllocation::from(
                    quantity: 1,
                    allocatedTo: 'user-1',
                    allocatedAt: Clock::now(),
                ),
                TicketAllocation::from(
                    quantity: 1,
                    allocatedTo: 'user-2',
                    allocatedAt: Clock::now(),
                ),
                TicketAllocation::from(
                    quantity: 1,
                    allocatedTo: 'user-3',
                    allocatedAt: Clock::now(),
                ),
            ],
            'expectedNumberOfTicketAllocations' => 3,
        ];

        yield 'multiple tickets with different quantities' => [
            'ticketAllocationCandidates' => [
                TicketAllocation::from(
                    quantity: 3,
                    allocatedTo: 'user-1',
                    allocatedAt: Clock::now(),
                ),
                TicketAllocation::from(
                    quantity: 90,
                    allocatedTo: 'user-2',
                    allocatedAt: Clock::now(),
                ),
                TicketAllocation::from(
                    quantity: 832,
                    allocatedTo: 'user-3',
                    allocatedAt: Clock::now(),
                ),
            ],
            'expectedNumberOfTicketAllocations' => 925,
        ];
    }
}

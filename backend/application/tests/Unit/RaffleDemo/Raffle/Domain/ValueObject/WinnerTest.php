<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidWinnerException;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use App\RaffleDemo\Raffle\Domain\ValueObject\Winner;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WinnerTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $ticketAllocation = TicketAllocation::from(quantity: 1, allocatedTo: 'participant', allocatedAt: Clock::now());
        $ticketNumber = 1;

        // Act
        $winner = Winner::fromWinningTicketAllocation($ticketAllocation, $ticketNumber);

        // Assert
        self::assertSame(
            ['ticketAllocation' => $ticketAllocation->toArray(), 'ticketNumber' => $ticketNumber],
            $winner->toArray(),
        );
    }

    #[Test]
    public function it_fails_when_the_ticket_number_is_less_than_1(): void
    {
        // Arrange
        $ticketAllocation = TicketAllocation::from(quantity: 1, allocatedTo: 'participant', allocatedAt: Clock::now());
        $ticketNumber = 0;

        // Act
        self::expectExceptionMessage(InvalidWinnerException::fromLessThan1()->getMessage());

        Winner::fromWinningTicketAllocation($ticketAllocation, $ticketNumber);

        // Assert
        self::fail();
    }
}

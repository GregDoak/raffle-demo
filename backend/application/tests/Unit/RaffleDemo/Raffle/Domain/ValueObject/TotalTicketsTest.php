<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\ValueObject;

use App\RaffleDemo\Raffle\Domain\Exception\InvalidTotalTicketsException;
use App\RaffleDemo\Raffle\Domain\ValueObject\TotalTickets;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TotalTicketsTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $value = 100;

        // Act
        $totalTickets = TotalTickets::fromInt($value);

        // Assert
        self::assertSame($value, $totalTickets->toInt());
    }

    #[Test]
    public function it_fails_when_the_total_tickets_is_less_than_1(): void
    {
        // Arrange
        $value = 0;

        // Act
        self::expectExceptionMessage(InvalidTotalTicketsException::fromLessThan1()->getMessage());

        TotalTickets::fromInt($value);

        // Assert
        self::fail();
    }
}

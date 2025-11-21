<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\ValueObject;

use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketPriceException;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketPrice;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TicketPriceTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $amount = 0;
        $currency = 'GBP';

        // Act
        $ticketPrice = TicketPrice::from($amount, $currency);

        // Assert
        self::assertSame(['amount' => $amount, 'currency' => $currency], $ticketPrice->toArray());
    }

    #[Test]
    public function it_fails_when_the_amount_is_less_than_zero(): void
    {
        // Arrange
        $amount = -1;
        $currency = 'GBP';

        // Act
        self::expectExceptionMessage(InvalidTicketPriceException::fromNegativeAmount()->getMessage());

        TicketPrice::from($amount, $currency);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_the_currency_is_empty(): void
    {
        // Arrange
        $amount = 1000;
        $currency = '';

        // Act
        self::expectExceptionMessage(InvalidTicketPriceException::fromEmptyCurrency()->getMessage());

        TicketPrice::from($amount, $currency);

        // Assert
        self::fail();
    }
}

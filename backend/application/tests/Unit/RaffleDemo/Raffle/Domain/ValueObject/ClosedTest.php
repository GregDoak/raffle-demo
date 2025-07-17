<?php

declare(strict_types=1);

namespace App\Tests\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidClosedException;
use App\RaffleDemo\Raffle\Domain\ValueObject\Closed;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClosedTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $by = 'user';
        $at = Clock::now();

        // Act
        $closed = Closed::from($by, $at);

        // Assert
        self::assertSame(['by' => $by, 'at' => $at->format(DATE_ATOM)], $closed->toArray());
    }

    #[Test]
    public function it_fails_when_the_by_is_empty(): void
    {
        // Arrange
        $by = '';
        $at = Clock::now();

        // Act
        self::expectExceptionMessage(InvalidClosedException::fromEmptyBy()->getMessage());

        Closed::from($by, $at);

        // Assert
        self::fail();
    }
}

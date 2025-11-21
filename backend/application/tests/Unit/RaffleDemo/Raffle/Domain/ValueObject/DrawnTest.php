<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidDrawnException;
use App\RaffleDemo\Raffle\Domain\ValueObject\Drawn;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DrawnTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $by = 'user';
        $at = Clock::now();

        // Act
        $drawn = Drawn::from($by, $at);

        // Assert
        self::assertSame(['by' => $by, 'at' => $at->format(DATE_ATOM)], $drawn->toArray());
    }

    #[Test]
    public function it_fails_when_the_by_is_empty(): void
    {
        // Arrange
        $by = '';
        $at = Clock::now();

        // Act
        self::expectExceptionMessage(InvalidDrawnException::fromEmptyBy()->getMessage());

        Drawn::from($by, $at);

        // Assert
        self::fail();
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidStartedException;
use App\RaffleDemo\Raffle\Domain\ValueObject\Started;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StartedTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $by = 'user';
        $at = Clock::now();

        // Act
        $started = Started::from($by, $at);

        // Assert
        self::assertSame(['by' => $by, 'at' => $at->format(DATE_ATOM)], $started->toArray());
    }

    #[Test]
    public function it_fails_when_the_by_is_empty(): void
    {
        // Arrange
        $by = '';
        $at = Clock::now();

        // Act
        self::expectExceptionMessage(InvalidStartedException::fromEmptyBy()->getMessage());

        Started::from($by, $at);

        // Assert
        self::fail();
    }
}

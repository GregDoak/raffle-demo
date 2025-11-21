<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidEndedException;
use App\RaffleDemo\Raffle\Domain\ValueObject\Ended;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EndedTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $by = 'user';
        $at = Clock::now();
        $reason = 'reason';

        // Act
        $ended = Ended::from($by, $at, $reason);

        // Assert
        self::assertSame(['by' => $by, 'at' => $at->format(DATE_ATOM), 'reason' => $reason], $ended->toArray());
    }

    #[Test]
    public function it_fails_when_the_by_is_empty(): void
    {
        // Arrange
        $by = '';
        $at = Clock::now();
        $reason = 'reason';

        // Act
        self::expectExceptionMessage(InvalidEndedException::fromEmptyBy()->getMessage());

        Ended::from($by, $at, $reason);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_the_reason_is_empty(): void
    {
        // Arrange
        $by = 'user';
        $at = Clock::now();
        $reason = '';

        // Act
        self::expectExceptionMessage(InvalidEndedException::fromEmptyReason()->getMessage());

        Ended::from($by, $at, $reason);

        // Assert
        self::fail();
    }
}

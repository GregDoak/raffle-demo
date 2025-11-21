<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidCreatedException;
use App\RaffleDemo\Raffle\Domain\ValueObject\Created;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CreatedTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $by = 'user';
        $at = Clock::now();

        // Act
        $created = Created::from($by, $at);

        // Assert
        self::assertSame(['by' => $by, 'at' => $at->format(DATE_ATOM)], $created->toArray());
    }

    #[Test]
    public function it_fails_when_the_by_is_empty(): void
    {
        // Arrange
        $by = '';
        $at = Clock::now();

        // Act
        self::expectExceptionMessage(InvalidCreatedException::fromEmptyBy()->getMessage());

        Created::from($by, $at);

        // Assert
        self::fail();
    }
}

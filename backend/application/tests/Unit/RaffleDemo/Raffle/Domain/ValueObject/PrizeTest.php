<?php

declare(strict_types=1);

namespace App\Tests\RaffleDemo\Raffle\Domain\ValueObject;

use App\RaffleDemo\Raffle\Domain\Exception\InvalidPrizeException;
use App\RaffleDemo\Raffle\Domain\ValueObject\Prize;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function strlen;

final class PrizeTest extends TestCase
{
    #[Test]
    public function it_can_be_created_when_the_prize_is_3_characters(): void
    {
        // Arrange
        $value = self::generatePrize(3);

        // Act
        $prize = Prize::fromString($value);

        // Assert
        self::assertSame($value, $prize->toString());
    }

    #[Test]
    public function it_can_be_created_when_the_prize_is_200_characters(): void
    {
        // Arrange
        $value = self::generatePrize(200);

        // Act
        $prize = Prize::fromString($value);

        // Assert
        self::assertSame($value, $prize->toString());
    }

    #[Test]
    public function it_fails_when_the_prize_is_empty(): void
    {
        // Arrange
        $value = '';

        // Act
        self::expectExceptionMessage(InvalidPrizeException::fromEmptyPrize()->getMessage());

        Prize::fromString($value);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_the_prize_is_less_than_3_characters(): void
    {
        // Arrange
        $value = self::generatePrize(2);
        // Act
        self::expectExceptionMessage(InvalidPrizeException::fromTooShort()->getMessage());

        Prize::fromString($value);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_the_prize_is_greater_than_200_characters(): void
    {
        // Arrange
        $value = self::generatePrize(201);
        // Act
        self::expectExceptionMessage(InvalidPrizeException::fromTooLong()->getMessage());

        Prize::fromString($value);

        // Assert
        self::fail();
    }

    private static function generatePrize(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

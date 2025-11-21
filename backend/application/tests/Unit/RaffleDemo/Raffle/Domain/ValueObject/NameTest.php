<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\ValueObject;

use App\RaffleDemo\Raffle\Domain\Exception\InvalidNameException;
use App\RaffleDemo\Raffle\Domain\ValueObject\Name;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function strlen;

final class NameTest extends TestCase
{
    #[Test]
    public function it_can_be_created_when_the_name_is_3_characters(): void
    {
        // Arrange
        $value = self::generateName(3);

        // Act
        $name = Name::fromString($value);

        // Assert
        self::assertSame($value, $name->toString());
    }

    #[Test]
    public function it_can_be_created_when_the_name_is_200_characters(): void
    {
        // Arrange
        $value = self::generateName(200);

        // Act
        $name = Name::fromString($value);

        // Assert
        self::assertSame($value, $name->toString());
    }

    #[Test]
    public function it_fails_when_the_name_is_empty(): void
    {
        // Arrange
        $value = '';

        // Act
        self::expectExceptionMessage(InvalidNameException::fromEmptyName()->getMessage());

        Name::fromString($value);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_the_name_is_less_than_3_characters(): void
    {
        // Arrange
        $value = self::generateName(2);
        // Act
        self::expectExceptionMessage(InvalidNameException::fromTooShort()->getMessage());

        Name::fromString($value);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_the_name_is_greater_than_200_characters(): void
    {
        // Arrange
        $value = self::generateName(201);
        // Act
        self::expectExceptionMessage(InvalidNameException::fromTooLong()->getMessage());

        Name::fromString($value);

        // Assert
        self::fail();
    }

    private static function generateName(int $length): string
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

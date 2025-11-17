<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Notification\Domain\ValueObject;

use App\RaffleDemo\Notification\Domain\Exception\InvalidEmailAddressException;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmailAddressTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $value = 'email@example.com';

        // Act
        $emailAddress = EmailAddress::fromString($value);

        // Assert
        self::assertSame($value, $emailAddress->toString());
    }

    #[Test]
    public function it_fails_when_the_email_is_empty(): void
    {
        // Arrange
        $value = '';

        // Act
        self::expectExceptionMessage(InvalidEmailAddressException::fromEmptyEmailAddress()->getMessage());

        EmailAddress::fromString($value);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_the_email_is_invalid(): void
    {
        // Arrange
        $value = 'INVALID';

        // Act
        self::expectExceptionMessage(InvalidEmailAddressException::fromInvalidEmailAddress()->getMessage());

        EmailAddress::fromString($value);

        // Assert
        self::fail();
    }
}

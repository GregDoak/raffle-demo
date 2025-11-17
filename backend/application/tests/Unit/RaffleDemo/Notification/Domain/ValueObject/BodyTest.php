<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Notification\Domain\ValueObject;

use App\RaffleDemo\Notification\Domain\Exception\InvalidBodyException;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BodyTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $value = 'body';

        // Act
        $body = Body::fromString($value);

        // Assert
        self::assertSame($value, $body->toString());
    }

    #[Test]
    public function it_fails_when_the_body_is_empty(): void
    {
        // Arrange
        $value = '';

        // Act
        self::expectExceptionMessage(InvalidBodyException::fromEmptyBody()->getMessage());

        Body::fromString($value);

        // Assert
        self::fail();
    }
}

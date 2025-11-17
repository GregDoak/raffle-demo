<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Notification\Domain\ValueObject;

use App\RaffleDemo\Notification\Domain\ValueObject\AbstractRecipient;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TypeError;

final class RecipientCollectionTest extends TestCase
{
    #[Test]
    public function it_can_create_from_new(): void
    {
        // Arrange
        $value = [];

        // Act
        $collection = RecipientCollection::fromNew();

        // Assert
        self::assertSame($value, $collection->getRecipients());
    }

    #[Test]
    public function it_can_create_from_array(): void
    {
        // Arrange
        $values = [EmailAddress::fromString('recipient.1@example.com'), EmailAddress::fromString('recipient.2@example.com')];

        // Act
        $collection = RecipientCollection::fromArray(...$values);

        // Assert
        self::assertSame($values, $collection->getRecipients());
    }

    #[Test]
    public function it_can_serialize_into_an_array(): void
    {
        // Arrange
        /** @var AbstractRecipient[] $values */
        $values = [EmailAddress::fromString('recipient.1@example.com'), EmailAddress::fromString('recipient.2@example.com')];

        // Act
        $collection = RecipientCollection::fromArray(...$values);

        // Assert
        $recipients = $collection->toArray();
        self::assertSame($values[0]->toString(), $recipients[0]);
        self::assertSame($values[1]->toString(), $recipients[1]);
    }

    #[Test]
    public function it_fails_when_the_input_is_not_a_recipient(): void
    {
        // Arrange
        $values = ['string'];

        // Act
        self::expectException(TypeError::class);

        RecipientCollection::fromArray(...$values); // @phpstan-ignore-line argument.type

        // Assert
        self::fail();
    }
}

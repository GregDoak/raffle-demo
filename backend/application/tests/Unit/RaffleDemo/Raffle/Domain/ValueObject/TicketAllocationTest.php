<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\ValueObject;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketAllocationException;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function strlen;

final class TicketAllocationTest extends TestCase
{
    #[Test]
    public function it_can_be_created(): void
    {
        // Arrange
        $quantity = 1;
        $allocatedTo = self::generateAllocateTo(200);
        $allocatedAt = Clock::now();

        // Act
        $ticketAllocation = TicketAllocation::from($quantity, $allocatedTo, $allocatedAt);

        // Assert
        self::assertSame(
            ['quantity' => $quantity, 'allocatedTo' => $allocatedTo, 'allocatedAt' => $allocatedAt->format(DATE_ATOM)],
            $ticketAllocation->toArray(),
        );
    }

    #[Test]
    public function it_fails_when_the_quantity_is_less_than_1(): void
    {
        // Arrange
        $quantity = 0;
        $allocatedTo = 'participant';
        $allocatedAt = Clock::now();

        // Act
        self::expectExceptionMessage(InvalidTicketAllocationException::fromInvalidQuantity()->getMessage());

        TicketAllocation::from($quantity, $allocatedTo, $allocatedAt);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_the_allocated_to_is_empty(): void
    {
        // Arrange
        $quantity = 1;
        $allocatedTo = '';
        $allocatedAt = Clock::now();

        // Act
        self::expectExceptionMessage(InvalidTicketAllocationException::fromEmptyAllocatedTo()->getMessage());

        TicketAllocation::from($quantity, $allocatedTo, $allocatedAt);

        // Assert
        self::fail();
    }

    #[Test]
    public function it_fails_when_the_allocated_to_is_greater_than_200_characters(): void
    {
        // Arrange
        $quantity = 1;
        $allocatedTo = self::generateAllocateTo(201);
        $allocatedAt = Clock::now();

        // Act
        self::expectExceptionMessage(InvalidTicketAllocationException::fromAllocatedToTooLong()->getMessage());

        TicketAllocation::from($quantity, $allocatedTo, $allocatedAt);

        // Assert
        self::fail();
    }

    private static function generateAllocateTo(int $length): string
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

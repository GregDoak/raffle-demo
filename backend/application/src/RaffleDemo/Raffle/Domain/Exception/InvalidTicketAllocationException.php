<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidTicketAllocationException extends AbstractInvariantViolationException
{
    public static function fromAllocatedToTooLong(): self
    {
        return self::fromMessage('The ticket allocated to cannot be more than 200 characters.');
    }

    public static function fromCannotAllocateAfterClosed(): self
    {
        return self::fromMessage('The ticket allocation cannot happen after the raffle has closed.');
    }

    public static function fromCannotAllocateBeforeStarted(): self
    {
        return self::fromMessage('The ticket allocation cannot happen before the raffle has started.');
    }

    public static function fromDuplicateTicketAllocation(): self
    {
        return self::fromMessage('The ticket allocation cannot add a duplicate allocation.');
    }

    public static function fromEmptyAllocatedTo(): self
    {
        return self::fromMessage('The ticket allocated to is required and cannot be empty.');
    }

    public static function fromInvalidQuantity(): self
    {
        return self::fromMessage('The ticket allocation quantity must have at least 1.');
    }

    public static function fromOverAllocationOfTickets(): self
    {
        return self::fromMessage('The ticket allocation cannot over allocate tickets to the raffle.');
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidTicketAllocationsException extends AbstractInvariantViolationException
{
    public static function fromCannotDrawUnallocatedTicket(): self
    {
        return self::fromMessage('The drawn ticket was not allocated.');
    }
}

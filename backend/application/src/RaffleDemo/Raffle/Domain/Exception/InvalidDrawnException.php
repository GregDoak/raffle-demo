<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidDrawnException extends AbstractInvariantViolationException
{
    public static function fromAlreadyDrawn(): self
    {
        return self::fromMessage('The raffle is already drawn.');
    }

    public static function fromCannotDrawPrizeBeforeClosed(): self
    {
        return self::fromMessage('The raffle cannot be drawn until the raffle is closed.');
    }

    public static function fromCannotDrawWhenNoTicketAllocations(): self
    {
        return self::fromMessage('The raffle cannot be drawn if no tickets are allocated.');
    }

    public static function fromEmptyBy(): self
    {
        return self::fromMessage('The by is required and cannot be empty.');
    }
}

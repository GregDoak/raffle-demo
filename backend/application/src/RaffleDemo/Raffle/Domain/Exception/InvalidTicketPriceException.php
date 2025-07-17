<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidTicketPriceException extends AbstractInvariantViolationException
{
    public static function fromNegativeAmount(): self
    {
        return self::fromMessage('The ticket price must be greater than 0.');
    }

    public static function fromEmptyCurrency(): self
    {
        return self::fromMessage('The ticket currency is required and cannot be empty.');
    }
}

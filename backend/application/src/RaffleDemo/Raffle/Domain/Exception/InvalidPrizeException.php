<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidPrizeException extends AbstractInvariantViolationException
{
    public static function fromEmptyPrize(): self
    {
        return self::fromMessage('The prize is required and cannot be empty.');
    }

    public static function fromTooShort(): self
    {
        return self::fromMessage('The prize must be at least 3 characters.');
    }

    public static function fromTooLong(): self
    {
        return self::fromMessage('The prize cannot be more than 200 characters.');
    }
}

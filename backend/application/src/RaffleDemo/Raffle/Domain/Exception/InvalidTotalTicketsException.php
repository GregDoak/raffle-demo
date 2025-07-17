<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidTotalTicketsException extends AbstractInvariantViolationException
{
    public static function fromLessThan1(): self
    {
        return self::fromMessage('The total tickets amount must be greater or equal to 1.');
    }
}

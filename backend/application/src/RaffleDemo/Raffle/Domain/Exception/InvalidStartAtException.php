<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidStartAtException extends AbstractInvariantViolationException
{
    public static function fromStartAtLessThan1DayBeforeCloseAt(): self
    {
        return self::fromMessage('The start at date must at least 1 day before the close at date.');
    }
}

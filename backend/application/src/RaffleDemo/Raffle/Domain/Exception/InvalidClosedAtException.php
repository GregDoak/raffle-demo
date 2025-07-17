<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidClosedAtException extends AbstractInvariantViolationException
{
    public static function fromCloseAtAfterDrawAt(): self
    {
        return self::fromMessage('The close at date must be before the draw at date.');
    }
}

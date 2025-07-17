<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidStartedException extends AbstractInvariantViolationException
{
    public static function fromAlreadyStarted(): self
    {
        return self::fromMessage('The raffle is already started.');
    }

    public static function fromCannotStartBeforeStartAtDate(): self
    {
        return self::fromMessage('The raffle cannot be started before the start at date.');
    }

    public static function fromEmptyBy(): self
    {
        return self::fromMessage('The by is required and cannot be empty.');
    }
}

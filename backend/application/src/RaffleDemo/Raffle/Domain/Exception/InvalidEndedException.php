<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidEndedException extends AbstractInvariantViolationException
{
    public static function fromAlreadyEnded(): self
    {
        return self::fromMessage('The raffle is already ended.');
    }

    public static function fromCannotEndBeforeClosed(): self
    {
        return self::fromMessage('The raffle cannot be ended before the raffle is closed.');
    }

    public static function fromEmptyBy(): self
    {
        return self::fromMessage('The by is required and cannot be empty.');
    }

    public static function fromEmptyReason(): self
    {
        return self::fromMessage('The reason is required and cannot be empty.');
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidClosedException extends AbstractInvariantViolationException
{
    public static function fromEmptyBy(): self
    {
        return self::fromMessage('The by is required and cannot be empty.');
    }

    public static function fromAlreadyClosed(): self
    {
        return self::fromMessage('The raffle is already closed.');
    }
}

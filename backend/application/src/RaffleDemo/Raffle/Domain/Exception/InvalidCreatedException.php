<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidCreatedException extends AbstractInvariantViolationException
{
    public static function fromEmptyBy(): self
    {
        return self::fromMessage('The by is required and cannot be empty.');
    }

    public static function fromCreatedAtAfterStartAt(): self
    {
        return self::fromMessage('The created at date must be before the start date.');
    }
}

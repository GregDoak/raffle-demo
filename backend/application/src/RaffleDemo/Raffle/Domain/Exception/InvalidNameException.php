<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidNameException extends AbstractInvariantViolationException
{
    public static function fromEmptyName(): self
    {
        return self::fromMessage('The name is required and cannot be empty.');
    }

    public static function fromTooShort(): self
    {
        return self::fromMessage('The name must be at least 3 characters.');
    }

    public static function fromTooLong(): self
    {
        return self::fromMessage('The name cannot be more than 200 characters.');
    }
}

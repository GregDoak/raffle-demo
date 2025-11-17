<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidEmailAddressException extends AbstractInvariantViolationException
{
    public static function fromEmptyEmailAddress(): self
    {
        return self::fromMessage('The email address is required and cannot be empty.');
    }

    public static function fromInvalidEmailAddress(): self
    {
        return self::fromMessage('The email address is not a valid email address.');
    }
}

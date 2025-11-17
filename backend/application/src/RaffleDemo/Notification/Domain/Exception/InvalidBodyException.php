<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidBodyException extends AbstractInvariantViolationException
{
    public static function fromEmptyBody(): self
    {
        return self::fromMessage('The body is required and cannot be empty.');
    }
}

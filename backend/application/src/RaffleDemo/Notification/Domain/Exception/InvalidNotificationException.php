<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidNotificationException extends AbstractInvariantViolationException
{
    public static function fromDuplicateNotification(): self
    {
        return self::fromMessage('This notification has already been handled.');
    }
}

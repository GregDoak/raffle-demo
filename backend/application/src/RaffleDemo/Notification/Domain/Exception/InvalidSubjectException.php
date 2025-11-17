<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidSubjectException extends AbstractInvariantViolationException
{
    public static function fromEmptySubject(): self
    {
        return self::fromMessage('The subject is required and cannot be empty.');
    }
}

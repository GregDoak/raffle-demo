<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;

final class InvalidWinnerException extends AbstractInvariantViolationException
{
    public static function fromLessThan1(): self
    {
        return self::fromMessage('The winning ticket number must be greater or equal to 1.');
    }
}

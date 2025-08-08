<?php

declare(strict_types=1);

namespace App\Framework\Domain\Exception;

final class InvalidAggregateIdException extends AbstractInvariantViolationException
{
    public static function fromInvalidId(): self
    {
        return self::fromMessage('The id must is not a valid id.');
    }
}

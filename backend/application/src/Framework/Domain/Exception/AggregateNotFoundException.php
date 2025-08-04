<?php

declare(strict_types=1);

namespace App\Framework\Domain\Exception;

use RuntimeException;

final class AggregateNotFoundException extends RuntimeException
{
    public static function fromAggregateId(): self
    {
        return new self('The requested id was not found.');
    }
}

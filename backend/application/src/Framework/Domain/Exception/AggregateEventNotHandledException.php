<?php

declare(strict_types=1);

namespace App\Framework\Domain\Exception;

use DomainException;

use function sprintf;

final class AggregateEventNotHandledException extends DomainException
{
    public static function notHandledByAggregate(string $eventClassName, string $aggregateClassName): self
    {
        return new self(sprintf('The aggregate event "%s" was not handled by "%s".', $eventClassName, $aggregateClassName));
    }
}

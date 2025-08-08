<?php

declare(strict_types=1);

namespace App\Framework\Domain\Exception;

use RuntimeException;

use function sprintf;

final class AggregateEventNotHandledException extends RuntimeException
{
    public static function notHandledByAggregate(string $eventClassName, string $aggregateClassName): self
    {
        return new self(sprintf('The aggregate event "%s" was not handled by "%s".', $eventClassName, $aggregateClassName));
    }

    public static function notHandledByEventFactory(string $eventName, string $factoryClassName): self
    {
        return new self(sprintf('The aggregate event "%s" was not handled by "%s".', $eventName, $factoryClassName));
    }
}

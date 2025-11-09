<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger\Serializer;

use RuntimeException;

use function sprintf;

final class UnknownDomainEventTypeException extends RuntimeException
{
    public function __construct(string $type)
    {
        parent::__construct(sprintf('Unknown domain event type "%s"', $type));
    }
}

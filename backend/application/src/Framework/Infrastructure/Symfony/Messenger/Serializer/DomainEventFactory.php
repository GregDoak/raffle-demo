<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger\Serializer;

use App\Foundation\Clock\Clock;
use App\Foundation\DomainEventRegistry\DomainEventInterface;
use App\Foundation\Serializer\JsonSerializer;
use CloudEvents\V1\CloudEventInterface;

use function is_string;

final readonly class DomainEventFactory
{
    public static function fromCloudEvent(CloudEventInterface $cloudEvent): DomainEventInterface
    {
        $eventId = $cloudEvent->getId();
        $eventOccurredAt = Clock::fromNullableString($cloudEvent->getTime()?->format(DATE_ATOM));
        $payload = (array) JsonSerializer::deserialize(is_string($cloudEvent->getData()) ? $cloudEvent->getData() : '{}');

        return match ($cloudEvent->getType()) {
            default => throw new UnknownDomainEventTypeException($cloudEvent->getType())
        };
    }
}

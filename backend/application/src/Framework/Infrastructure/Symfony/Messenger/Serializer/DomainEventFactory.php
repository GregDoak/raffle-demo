<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger\Serializer;

use App\Foundation\Clock\Clock;
use App\Foundation\DomainEventRegistry\DomainEventInterface;
use App\Foundation\DomainEventRegistry\Raffle\RaffleCreatedV1Event;
use App\Foundation\Serializer\JsonSerializer;
use CloudEvents\V1\CloudEventInterface;

use function is_string;

final readonly class DomainEventFactory
{
    public static function fromCloudEvent(CloudEventInterface $cloudEvent): DomainEventInterface
    {
        $eventId = $cloudEvent->getId();
        $eventOccurredAt = Clock::fromNullableString($cloudEvent->getTime()?->format(DATE_ATOM)) ?? Clock::now();
        $payload = (array) JsonSerializer::deserialize(is_string($cloudEvent->getData()) ? $cloudEvent->getData() : '{}');

        return match ($cloudEvent->getType()) {
            'raffle_demo.raffle.created.v1' => RaffleCreatedV1Event::fromPayload($eventId, $eventOccurredAt, $payload), // @phpstan-ignore-line argument.type
            default => throw new UnknownDomainEventTypeException($cloudEvent->getType())
        };
    }
}

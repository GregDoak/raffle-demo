<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger\Serializer;

use App\Foundation\DomainEventRegistry\DomainEventInterface;
use App\Foundation\Serializer\JsonSerializer;
use App\Framework\Infrastructure\Symfony\Messenger\Middleware\RetryStamp;
use CloudEvents\Serializers\Normalizers\V1\Denormalizer;
use CloudEvents\Serializers\Normalizers\V1\Normalizer;
use CloudEvents\V1\CloudEventImmutable;
use DateTimeImmutable;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

final readonly class CloudEventsJsonSerializer implements DomainEventSerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $headers = $encodedEnvelope['headers'] ?? [];
        $decodedBody = (array) JsonSerializer::deserialize($encodedEnvelope['body']);
        $cloudEvent = new Denormalizer()->denormalize($decodedBody);
        $retryCount = (int) ($headers['retry_count'] ?? '0') + 1;

        $domainEvent = DomainEventFactory::fromCloudEvent($cloudEvent);

        return new Envelope($domainEvent, [new TransportMessageIdStamp($domainEvent->getEventId()), new BusNameStamp('domain_event.bus'), new RetryStamp($retryCount)]);
    }

    public function encode(Envelope $envelope): array
    {
        /** @var DomainEventInterface $event */
        $event = $envelope->getMessage();
        $retryCount = $envelope->last(RetryStamp::class)->retryCount ?? 0;

        $cloudEvent = new CloudEventImmutable(
            id: $event->getEventId(),
            source: 'raffle-demo',
            type: $event->getEventType(),
            data: JsonSerializer::serialize($event->serialize()),
            dataContentType: 'application/json',
            time: new DateTimeImmutable($event->getEventOccurredAt()->format(DATE_ATOM)),
        );

        return [
            'body' => JsonSerializer::serialize(new Normalizer()->normalize($cloudEvent, rawData: false)),
            'headers' => ['type' => $event->getEventType(), 'retry_count' => (string) $retryCount],
        ];
    }
}

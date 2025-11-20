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

use function is_array;
use function is_int;
use function is_string;

final readonly class CloudEventsJsonSerializer implements DomainEventSerializerInterface
{
    /** @param mixed[] $encodedEnvelope */
    public function decode(array $encodedEnvelope): Envelope
    {
        $headers = is_array($encodedEnvelope['headers']) ? $encodedEnvelope['headers'] : [];
        $decodedBody = (array) JsonSerializer::deserialize(is_string($encodedEnvelope['body']) ? $encodedEnvelope['body'] : '{}');
        $cloudEvent = new Denormalizer()->denormalize($decodedBody);
        $retryCount = (is_int($headers['retry_count']) ? $headers['retry_count'] : 0) + 1;

        $domainEvent = DomainEventFactory::fromCloudEvent($cloudEvent);

        return new Envelope($domainEvent, [new TransportMessageIdStamp($domainEvent->getEventId()), new BusNameStamp('domain_event.bus'), new RetryStamp($retryCount)]);
    }

    /** @return array{body: string, headers: array{type: string, retry_count: int}} */
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
            'headers' => ['type' => $event->getEventType(), 'retry_count' => $retryCount],
        ];
    }
}

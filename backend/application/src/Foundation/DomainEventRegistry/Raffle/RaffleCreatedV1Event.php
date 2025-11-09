<?php

declare(strict_types=1);

namespace App\Foundation\DomainEventRegistry\Raffle;

use App\Foundation\Clock\Clock;
use App\Foundation\DomainEventRegistry\DomainEventInterface;
use App\Foundation\Uuid\Uuid;
use DateTimeInterface;

final readonly class RaffleCreatedV1Event implements DomainEventInterface
{
    private const string TYPE = 'raffle_demo.raffle.created.v1';

    private function __construct(
        public string $eventId,
        public DateTimeInterface $eventOccurredAt,
        public string $id,
        public string $name,
        public string $prize,
        public DateTimeInterface $createdAt,
        public string $createdBy,
        public DateTimeInterface $startAt,
        public DateTimeInterface $closeAt,
        public DateTimeInterface $drawAt,
        public int $totalTickets,
        public int $ticketAmount,
        public string $ticketCurrency,
    ) {
    }

    public static function fromNew(
        string $id,
        string $name,
        string $prize,
        DateTimeInterface $createdAt,
        string $createdBy,
        DateTimeInterface $startAt,
        DateTimeInterface $closeAt,
        DateTimeInterface $drawAt,
        int $totalTickets,
        int $ticketAmount,
        string $ticketCurrency,
    ): self {
        return new self(
            eventId: Uuid::v4(),
            eventOccurredAt: Clock::now(),
            id: $id,
            name: $name,
            prize: $prize,
            createdAt: $createdAt,
            createdBy: $createdBy,
            startAt: $startAt,
            closeAt: $closeAt,
            drawAt: $drawAt,
            totalTickets: $totalTickets,
            ticketAmount: $ticketAmount,
            ticketCurrency: $ticketCurrency,
        );
    }

    /** @param array{
     *     id: string,
     *     name: string,
     *     prize: string,
     *     createdAt: string,
     *     createdBy: string,
     *     startAt: string,
     *     closeAt: string,
     *     drawAt: string,
     *     totalTickets: int,
     *     ticketAmount: int,
     *     ticketCurrency: string
     * } $payload
     */
    public static function fromPayload(string $eventId, DateTimeInterface $eventOccurredAt, array $payload): self
    {
        return new self(
            eventId: $eventId,
            eventOccurredAt: $eventOccurredAt,
            id: $payload['id'],
            name: $payload['name'],
            prize: $payload['prize'],
            createdAt: Clock::fromString($payload['createdAt']),
            createdBy: $payload['createdBy'],
            startAt: Clock::fromString($payload['startAt']),
            closeAt: Clock::fromString($payload['closeAt']),
            drawAt: Clock::fromString($payload['drawAt']),
            totalTickets: $payload['totalTickets'],
            ticketAmount: $payload['ticketAmount'],
            ticketCurrency: $payload['ticketCurrency'],
        );
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getEventOccurredAt(): DateTimeInterface
    {
        return $this->eventOccurredAt;
    }

    public function getEventType(): string
    {
        return self::TYPE;
    }

    public function serialize(): array
    {
        return [
            'eventId' => $this->eventId,
            'eventOccurredAt' => $this->eventOccurredAt->format(DATE_ATOM),
            'id' => $this->id,
            'name' => $this->name,
            'prize' => $this->prize,
            'createdAt' => $this->createdAt->format(DATE_ATOM),
            'createdBy' => $this->createdBy,
            'startAt' => $this->startAt->format(DATE_ATOM),
            'closeAt' => $this->closeAt->format(DATE_ATOM),
            'drawAt' => $this->drawAt->format(DATE_ATOM),
            'totalTickets' => $this->totalTickets,
            'ticketAmount' => $this->ticketAmount,
            'ticketCurrency' => $this->ticketCurrency,
        ];
    }
}

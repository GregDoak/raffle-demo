<?php

declare(strict_types=1);

namespace App\Foundation\DomainEventRegistry\Raffle;

use App\Foundation\Clock\Clock;
use App\Foundation\DomainEventRegistry\DomainEventInterface;
use App\Foundation\Uuid\Uuid;
use DateTimeInterface;

final readonly class TicketAllocatedToParticipantV1Event implements DomainEventInterface
{
    private const string TYPE = 'raffle_demo.raffle.ticket_allocated_to_participant.v1';

    private function __construct(
        public string $eventId,
        public DateTimeInterface $eventOccurredAt,
        public string $id,
        public string $name,
        public string $prize,
        public DateTimeInterface $createdAt,
        public string $createdBy,
        public DateTimeInterface $startAt,
        public DateTimeInterface $allocatedAt,
        public int $allocatedQuantity,
        public string $allocatedTo,
        public DateTimeInterface $closeAt,
        public DateTimeInterface $drawAt,
        public int $totalTickets,
        public int $totalTicketsRemaining,
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
        DateTimeInterface $allocatedAt,
        int $allocatedQuantity,
        string $allocatedTo,
        DateTimeInterface $closeAt,
        DateTimeInterface $drawAt,
        int $totalTickets,
        int $totalTicketsRemaining,
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
            allocatedAt: $allocatedAt,
            allocatedQuantity: $allocatedQuantity,
            allocatedTo: $allocatedTo,
            closeAt: $closeAt,
            drawAt: $drawAt,
            totalTickets: $totalTickets,
            totalTicketsRemaining: $totalTicketsRemaining,
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
     *     allocatedAt: string,
     *     allocatedQuantity: int,
     *     allocatedTo: string,
     *     closeAt: string,
     *     drawAt: string,
     *     totalTickets: int,
     *     totalTicketsRemaining: int,
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
            allocatedAt: Clock::fromString($payload['allocatedAt']),
            allocatedQuantity: $payload['allocatedQuantity'],
            allocatedTo: $payload['allocatedTo'],
            closeAt: Clock::fromString($payload['closeAt']),
            drawAt: Clock::fromString($payload['drawAt']),
            totalTickets: $payload['totalTickets'],
            totalTicketsRemaining: $payload['totalTicketsRemaining'],
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
            'allocatedAt' => $this->allocatedAt->format(DATE_ATOM),
            'allocatedQuantity' => $this->allocatedQuantity,
            'allocatedTo' => $this->allocatedTo,
            'closeAt' => $this->closeAt->format(DATE_ATOM),
            'drawAt' => $this->drawAt->format(DATE_ATOM),
            'totalTickets' => $this->totalTickets,
            'totalTicketsRemaining' => $this->totalTicketsRemaining,
            'ticketAmount' => $this->ticketAmount,
            'ticketCurrency' => $this->ticketCurrency,
        ];
    }
}

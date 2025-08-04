<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\Raffle;

use DateTimeInterface;

final readonly class Raffle
{
    public function __construct(
        public string $id,
        public string $name,
        public string $prize,
        public DateTimeInterface $createdAt,
        public string $createdBy,
        public DateTimeInterface $startAt,
        public ?DateTimeInterface $startedAt,
        public ?string $startedBy,
        public int $totalTickets,
        public int $remainingTickets,
        public int $ticketAmount,
        public string $ticketCurrency,
        public DateTimeInterface $closeAt,
        public ?DateTimeInterface $closedAt,
        public ?string $closedBy,
        public DateTimeInterface $drawAt,
        public ?DateTimeInterface $drawnAt,
        public ?string $drawnBy,
        public ?string $winningAllocation,
        public ?int $winningTicketNumber,
        public ?string $wonBy,
        public DateTimeInterface $lastOccurredAt,
    ) {
    }

    public static function fromCreated(
        string $id,
        string $name,
        string $prize,
        DateTimeInterface $createdAt,
        string $createdBy,
        DateTimeInterface $startAt,
        int $totalTickets,
        int $remainingTickets,
        int $ticketAmount,
        string $ticketCurrency,
        DateTimeInterface $closeAt,
        DateTimeInterface $drawAt,
        DateTimeInterface $lastOccurredAt,
    ): self {
        return new self(
            id: $id,
            name: $name,
            prize: $prize,
            createdAt: $createdAt,
            createdBy: $createdBy,
            startAt: $startAt,
            startedAt: null,
            startedBy: null,
            totalTickets: $totalTickets,
            remainingTickets: $remainingTickets,
            ticketAmount: $ticketAmount,
            ticketCurrency: $ticketCurrency,
            closeAt: $closeAt,
            closedAt: null,
            closedBy: null,
            drawAt: $drawAt,
            drawnAt: null,
            drawnBy: null,
            winningAllocation: null,
            winningTicketNumber: null,
            wonBy: null,
            lastOccurredAt: $lastOccurredAt,
        );
    }

    public function started(
        DateTimeInterface $startedAt,
        string $startedBy,
        DateTimeInterface $lastOccurredAt,
    ): self {
        return new self(
            id: $this->id,
            name: $this->name,
            prize: $this->prize,
            createdAt: $this->createdAt,
            createdBy: $this->createdBy,
            startAt: $this->startAt,
            startedAt: $startedAt,
            startedBy: $startedBy,
            totalTickets: $this->totalTickets,
            remainingTickets: $this->remainingTickets,
            ticketAmount: $this->ticketAmount,
            ticketCurrency: $this->ticketCurrency,
            closeAt: $this->closeAt,
            closedAt: $this->closedAt,
            closedBy: $this->closedBy,
            drawAt: $this->drawAt,
            drawnAt: $this->drawnAt,
            drawnBy: $this->drawnBy,
            winningAllocation: $this->winningAllocation,
            winningTicketNumber: $this->winningTicketNumber,
            wonBy: $this->wonBy,
            lastOccurredAt: $lastOccurredAt,
        );
    }

    public function ticketAllocated(
        int $ticketAllocationQuantity,
        DateTimeInterface $lastOccurredAt,
    ): self {
        return new self(
            id: $this->id,
            name: $this->name,
            prize: $this->prize,
            createdAt: $this->createdAt,
            createdBy: $this->createdBy,
            startAt: $this->startAt,
            startedAt: $this->startedAt,
            startedBy: $this->startedBy,
            totalTickets: $this->totalTickets,
            remainingTickets: $this->remainingTickets - $ticketAllocationQuantity,
            ticketAmount: $this->ticketAmount,
            ticketCurrency: $this->ticketCurrency,
            closeAt: $this->closeAt,
            closedAt: $this->closedAt,
            closedBy: $this->closedBy,
            drawAt: $this->drawAt,
            drawnAt: $this->drawnAt,
            drawnBy: $this->drawnBy,
            winningAllocation: $this->winningAllocation,
            winningTicketNumber: $this->winningTicketNumber,
            wonBy: $this->wonBy,
            lastOccurredAt: $lastOccurredAt,
        );
    }

    public function closed(
        DateTimeInterface $closedAt,
        string $closedBy,
        DateTimeInterface $lastOccurredAt,
    ): self {
        return new self(
            id: $this->id,
            name: $this->name,
            prize: $this->prize,
            createdAt: $this->createdAt,
            createdBy: $this->createdBy,
            startAt: $this->startAt,
            startedAt: $this->startedAt,
            startedBy: $this->startedBy,
            totalTickets: $this->totalTickets,
            remainingTickets: $this->remainingTickets,
            ticketAmount: $this->ticketAmount,
            ticketCurrency: $this->ticketCurrency,
            closeAt: $this->closeAt,
            closedAt: $closedAt,
            closedBy: $closedBy,
            drawAt: $this->drawAt,
            drawnAt: $this->drawnAt,
            drawnBy: $this->drawnBy,
            winningAllocation: $this->winningAllocation,
            winningTicketNumber: $this->winningTicketNumber,
            wonBy: $this->wonBy,
            lastOccurredAt: $lastOccurredAt,
        );
    }

    public function drawn(
        DateTimeInterface $drawnAt,
        string $drawnBy,
        string $winningAllocation,
        int $winningTicketNumber,
        string $wonBy,
        DateTimeInterface $lastOccurredAt,
    ): self {
        return new self(
            id: $this->id,
            name: $this->name,
            prize: $this->prize,
            createdAt: $this->createdAt,
            createdBy: $this->createdBy,
            startAt: $this->startAt,
            startedAt: $this->startedAt,
            startedBy: $this->startedBy,
            totalTickets: $this->totalTickets,
            remainingTickets: $this->remainingTickets,
            ticketAmount: $this->ticketAmount,
            ticketCurrency: $this->ticketCurrency,
            closeAt: $this->closeAt,
            closedAt: $this->closedAt,
            closedBy: $this->closedBy,
            drawAt: $this->drawAt,
            drawnAt: $drawnAt,
            drawnBy: $drawnBy,
            winningAllocation: $winningAllocation,
            winningTicketNumber: $winningTicketNumber,
            wonBy: $wonBy,
            lastOccurredAt: $lastOccurredAt,
        );
    }
}

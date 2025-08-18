<?php

declare(strict_types=1);

namespace App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\Raffle;
use DateTimeInterface;

final readonly class RaffleProjectionDomainContext
{
    public static function create(
        string $id,
        ?string $name = null,
        ?string $prize = null,
        ?DateTimeInterface $createdAt = null,
        ?string $createdBy = null,
        ?DateTimeInterface $startAt = null,
        ?DateTimeInterface $startedAt = null,
        ?string $startedBy = null,
        ?int $totalTickets = null,
        ?int $remainingTickets = null,
        ?int $ticketAmount = null,
        ?string $ticketCurrency = null,
        ?DateTimeInterface $closeAt = null,
        ?DateTimeInterface $closedAt = null,
        ?string $closedBy = null,
        ?DateTimeInterface $drawAt = null,
        ?DateTimeInterface $drawnAt = null,
        ?string $drawnBy = null,
        ?string $winningAllocation = null,
        ?int $winningTicketNumber = null,
        ?string $wonBy = null,
        ?DateTimeInterface $lastOccurredAt = null,
    ): Raffle {
        return new Raffle(
            id: $id,
            name: $name ?? 'raffle-name',
            prize: $prize ?? 'raffle-prize',
            createdAt: $createdAt ?? Clock::now(),
            createdBy: $createdBy ?? 'created_by',
            startAt: $startAt ?? Clock::now(),
            startedAt: $startedAt ?? null,
            startedBy: $startedBy ?? null,
            totalTickets: $totalTickets ?? 100,
            remainingTickets: $remainingTickets ?? 110,
            ticketAmount: $ticketAmount ?? 120,
            ticketCurrency: $ticketCurrency ?? 'GBP',
            closeAt: $closeAt ?? Clock::now(),
            closedAt: $closedAt ?? null,
            closedBy: $closedBy ?? null,
            drawAt: $drawAt ?? Clock::now(),
            drawnAt: $drawnAt ?? null,
            drawnBy: $drawnBy ?? null,
            winningAllocation: $winningAllocation ?? null,
            winningTicketNumber: $winningTicketNumber ?? null,
            wonBy: $wonBy ?? null,
            lastOccurredAt: $lastOccurredAt ?? Clock::now(),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffles;

use App\Framework\Application\Query\ResultInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;

final readonly class GetRafflesResult implements ResultInterface
{
    /** @param array<int|string, array{
     *     id: string,
     *     name: string,
     *     prize: string,
     *     status: string,
     *     createdAt: string,
     *     createdBy: string,
     *     startAt: string,
     *     startedAt: ?string,
     *     startedBy: ?string,
     *     totalTickets: int,
     *     remainingTickets: int,
     *     ticketAmount: int,
     *     closeAt: string,
     *     closedAt: ?string,
     *     closedBy: ?string,
     *     drawAt: string,
     *     drawnAt: ?string,
     *     drawnBy: ?string,
     *     winningAllocation: ?string,
     *     winningTicketNumber: ?int,
     *     wonBy: ?string,
     *     endedAt: ?string,
     *     endedBy: ?string,
     *     endedReason: ?string
     * }> $raffles
     */
    public function __construct(
        public array $raffles,
        public int $total,
    ) {
    }

    public static function fromRaffles(int $total, Raffle ...$raffles): self
    {
        $raffles = array_map(
            static fn (Raffle $raffle) => [
                'id' => $raffle->id,
                'name' => $raffle->name,
                'prize' => $raffle->prize,
                'status' => $raffle->status,
                'createdAt' => $raffle->createdAt->format(DATE_ATOM),
                'createdBy' => $raffle->createdBy,
                'startAt' => $raffle->startAt->format(DATE_ATOM),
                'startedAt' => $raffle->startedAt?->format(DATE_ATOM),
                'startedBy' => $raffle->startedBy,
                'totalTickets' => $raffle->totalTickets,
                'remainingTickets' => $raffle->remainingTickets,
                'ticketAmount' => $raffle->ticketAmount,
                'ticketCurrency' => $raffle->ticketCurrency,
                'closeAt' => $raffle->closeAt->format(DATE_ATOM),
                'closedAt' => $raffle->closedAt?->format(DATE_ATOM),
                'closedBy' => $raffle->closedBy,
                'drawAt' => $raffle->drawAt->format(DATE_ATOM),
                'drawnAt' => $raffle->drawnAt?->format(DATE_ATOM),
                'drawnBy' => $raffle->drawnBy,
                'winningAllocation' => $raffle->winningAllocation,
                'winningTicketNumber' => $raffle->winningTicketNumber,
                'wonBy' => $raffle->wonBy,
                'endedAt' => $raffle->endedAt?->format(DATE_ATOM),
                'endedBy' => $raffle->endedBy,
                'endedReason' => $raffle->endedReason,
            ],
            $raffles,
        );

        return new self($raffles, $total);
    }
}

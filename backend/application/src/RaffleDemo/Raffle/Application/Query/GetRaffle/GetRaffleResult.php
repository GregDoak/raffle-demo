<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffle;

use App\Framework\Application\Query\ResultInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocation;

final readonly class GetRaffleResult implements ResultInterface
{
    /** @param ?array{
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
     *     allocations: array<int|string, array{
     *         allocatedAt: string,
     *         allocatedTo: string,
     *         quantity: int,
     *     }>,
     *     ticketAmount: int,
     *     ticketCurrency: string,
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
     * } $raffle
     */
    public function __construct(
        public ?array $raffle,
    ) {
    }

    public static function fromNull(): self
    {
        return new self(null);
    }

    public static function fromRaffle(Raffle $raffle, RaffleAllocation ...$raffleAllocations): self
    {
        /* @infection-ignore-all */
        return new self(
            [
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
                'allocations' => array_map(
                    static fn (RaffleAllocation $raffleAllocation) => [
                        'allocatedAt' => $raffleAllocation->allocatedAt->format(DATE_ATOM),
                        'allocatedTo' => $raffleAllocation->allocatedTo,
                        'quantity' => $raffleAllocation->quantity,
                    ],
                    $raffleAllocations,
                ),
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
        );
    }
}

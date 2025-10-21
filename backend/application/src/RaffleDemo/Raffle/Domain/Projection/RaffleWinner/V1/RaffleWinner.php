<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1;

use DateTimeInterface;

final readonly class RaffleWinner
{
    public function __construct(
        public string $raffleId,
        public string $raffleAllocationHash,
        public DateTimeInterface $drawnAt,
        public int $winningTicketNumber,
        public string $winner,
        public DateTimeInterface $lastOccurredAt,
    ) {
    }
}

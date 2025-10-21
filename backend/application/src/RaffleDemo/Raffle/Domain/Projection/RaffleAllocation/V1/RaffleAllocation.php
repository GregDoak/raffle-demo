<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1;

use DateTimeInterface;

final readonly class RaffleAllocation
{
    public function __construct(
        public string $raffleId,
        public string $hash,
        public DateTimeInterface $allocatedAt,
        public string $allocatedTo,
        public int $quantity,
        public DateTimeInterface $lastOccurredAt,
    ) {
    }
}

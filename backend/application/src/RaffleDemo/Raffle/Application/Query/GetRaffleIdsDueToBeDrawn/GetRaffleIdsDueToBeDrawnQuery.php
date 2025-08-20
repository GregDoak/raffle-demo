<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn;

use App\Framework\Application\Query\QueryInterface;
use DateTimeInterface;

final readonly class GetRaffleIdsDueToBeDrawnQuery implements QueryInterface
{
    private function __construct(
        public DateTimeInterface $drawAt,
    ) {
    }

    public static function create(DateTimeInterface $drawAt): self
    {
        return new self($drawAt);
    }
}

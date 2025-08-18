<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed;

use App\Framework\Application\Query\QueryInterface;
use DateTimeInterface;

final readonly class GetRaffleIdsDueToBeClosedQuery implements QueryInterface
{
    private function __construct(
        public DateTimeInterface $closeAt,
    ) {
    }

    public static function create(DateTimeInterface $closeAt): self
    {
        return new self($closeAt);
    }
}

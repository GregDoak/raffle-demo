<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeStarted;

use App\Framework\Application\Query\QueryInterface;
use DateTimeInterface;

final readonly class GetRaffleIdsDueToBeStartedQuery implements QueryInterface
{
    private function __construct(
        public DateTimeInterface $startAt,
    ) {
    }

    public static function create(DateTimeInterface $startAt): self
    {
        return new self($startAt);
    }
}

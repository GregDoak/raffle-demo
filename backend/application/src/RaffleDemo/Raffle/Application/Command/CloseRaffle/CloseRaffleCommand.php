<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\CloseRaffle;

use App\Framework\Application\Command\CommandInterface;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\ValueObject\Closed;
use DateTimeInterface;

final readonly class CloseRaffleCommand implements CommandInterface
{
    private function __construct(
        public RaffleAggregateId $id,
        public Closed $closed,
    ) {
    }

    public static function create(
        string $id,
        DateTimeInterface $closedAt,
        string $closedBy,
    ): self {
        return new self(
            RaffleAggregateId::fromString($id),
            Closed::from(by: $closedBy, at: $closedAt),
        );
    }
}

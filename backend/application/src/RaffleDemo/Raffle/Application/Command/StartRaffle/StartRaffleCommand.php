<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\StartRaffle;

use App\Framework\Application\Command\CommandInterface;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\ValueObject\Started;
use DateTimeInterface;

final readonly class StartRaffleCommand implements CommandInterface
{
    private function __construct(
        public RaffleAggregateId $id,
        public Started $started,
    ) {
    }

    public static function create(
        string $id,
        DateTimeInterface $startedAt,
        string $startedBy,
    ): self {
        return new self(
            RaffleAggregateId::fromString($id),
            Started::from(by: $startedBy, at: $startedAt),
        );
    }
}

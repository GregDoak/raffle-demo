<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\DrawPrize;

use App\Framework\Application\Command\CommandInterface;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\ValueObject\Drawn;
use DateTimeInterface;

final readonly class DrawPrizeCommand implements CommandInterface
{
    private function __construct(
        public RaffleAggregateId $id,
        public Drawn $drawn,
    ) {
    }

    public static function create(
        string $id,
        DateTimeInterface $drawnAt,
        string $drawnBy,
    ): self {
        return new self(
            RaffleAggregateId::fromString($id),
            Drawn::from(by: $drawnBy, at: $drawnAt),
        );
    }
}

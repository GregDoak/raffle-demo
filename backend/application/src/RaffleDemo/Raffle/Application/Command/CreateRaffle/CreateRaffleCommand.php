<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\CreateRaffle;

use App\Foundation\Clock\Clock;
use App\Framework\Application\Command\CommandInterface;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\ValueObject\CloseAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Created;
use App\RaffleDemo\Raffle\Domain\ValueObject\DrawAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Name;
use App\RaffleDemo\Raffle\Domain\ValueObject\Prize;
use App\RaffleDemo\Raffle\Domain\ValueObject\StartAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketPrice;
use App\RaffleDemo\Raffle\Domain\ValueObject\TotalTickets;

final readonly class CreateRaffleCommand implements CommandInterface
{
    private function __construct(
        public RaffleAggregateId $id,
        public Name $name,
        public Prize $prize,
        public StartAt $startAt,
        public CloseAt $closeAt,
        public DrawAt $drawAt,
        public TotalTickets $totalTickets,
        public TicketPrice $ticketPrice,
        public Created $created,
    ) {
    }

    /** @param array{amount: int, currency: string} $ticketPrice */
    public static function create(
        string $name,
        string $prize,
        string $startAt,
        string $closeAt,
        string $drawAt,
        int $totalTickets,
        array $ticketPrice,
        string $createdBy,
    ): self {
        return new self(
            RaffleAggregateId::fromNew(),
            Name::fromString($name),
            Prize::fromString($prize),
            StartAt::fromString($startAt),
            CloseAt::fromString($closeAt),
            DrawAt::fromString($drawAt),
            TotalTickets::fromInt($totalTickets),
            TicketPrice::fromArray($ticketPrice),
            Created::from($createdBy, Clock::now()),
        );
    }
}

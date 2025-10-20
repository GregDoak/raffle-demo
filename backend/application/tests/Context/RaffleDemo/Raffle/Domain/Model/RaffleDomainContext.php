<?php

declare(strict_types=1);

namespace App\Tests\Context\RaffleDemo\Raffle\Domain\Model;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\ValueObject\CloseAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Closed;
use App\RaffleDemo\Raffle\Domain\ValueObject\Created;
use App\RaffleDemo\Raffle\Domain\ValueObject\DrawAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Drawn;
use App\RaffleDemo\Raffle\Domain\ValueObject\Ended;
use App\RaffleDemo\Raffle\Domain\ValueObject\Name;
use App\RaffleDemo\Raffle\Domain\ValueObject\Prize;
use App\RaffleDemo\Raffle\Domain\ValueObject\StartAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Started;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketPrice;
use App\RaffleDemo\Raffle\Domain\ValueObject\TotalTickets;

final readonly class RaffleDomainContext
{
    public static function create(
        ?RaffleAggregateId $id = null,
        ?Name $name = null,
        ?Prize $prize = null,
        ?StartAt $startAt = null,
        ?CloseAt $closeAt = null,
        ?DrawAt $drawAt = null,
        ?TotalTickets $totalTickets = null,
        ?TicketPrice $ticketPrice = null,
        ?Created $created = null,
    ): Raffle {
        return Raffle::create(
            id: $id ?? RaffleAggregateId::fromNew(),
            name: $name ?? Name::fromString('raffle-name'),
            prize: $prize ?? Prize::fromString('raffle-prize'),
            startAt: $startAt ?? StartAt::fromString('2025-01-02 00:00:00'),
            closeAt: $closeAt ?? CloseAt::fromString('2025-01-03 00:00:00'),
            drawAt: $drawAt ?? DrawAt::fromString('2025-01-03 00:00:00'),
            totalTickets: $totalTickets ?? TotalTickets::fromInt(10),
            ticketPrice: $ticketPrice ?? TicketPrice::from(amount: 100, currency: 'GBP'),
            created: $created ?? Created::from(by: 'user', at: Clock::fromString('2025-01-01 00:00:00')),
        );
    }

    public static function start(Raffle $raffle, ?Started $started = null): Raffle
    {
        $raffle->start($started ?? Started::from(by: 'user', at: Clock::fromString('2025-01-02 00:00:01')));

        return $raffle;
    }

    public static function allocateTicketToParticipant(
        Raffle $raffle,
        ?TicketAllocation $ticketAllocation = null,
    ): Raffle {
        $raffle->allocateTicketToParticipant(
            $ticketAllocation ?? TicketAllocation::from(
                quantity: 1,
                allocatedTo: 'participant',
                allocatedAt: Clock::fromString('2025-01-02 00:00:01'),
            ),
        );

        return $raffle;
    }

    public static function close(Raffle $raffle, ?Closed $closed = null): Raffle
    {
        $raffle->close($closed ?? Closed::from(by: 'user', at: Clock::fromString('2025-01-03 00:00:00')));

        return $raffle;
    }

    public static function drawPrize(Raffle $raffle, ?Drawn $drawn = null): Raffle
    {
        $raffle->drawPrize($drawn ?? Drawn::from(by: 'user', at: Clock::fromString('2025-01-03 00:00:00')));

        return $raffle;
    }

    public static function end(Raffle $raffle, ?Ended $ended = null): Raffle
    {
        $raffle->end($ended ?? Ended::from(by: 'user', at: Clock::fromString('2025-01-03 00:00:00'), reason: 'reason'));

        return $raffle;
    }
}

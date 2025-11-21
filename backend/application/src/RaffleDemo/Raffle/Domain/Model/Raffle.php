<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Model;

use App\Framework\Domain\Exception\AggregateEventNotHandledException;
use App\Framework\Domain\Model\AbstractAggregate;
use App\Framework\Domain\Model\AggregateEvents;
use App\Framework\Domain\Model\Event\AggregateEventInterface;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidClosedAtException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidClosedException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidCreatedException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidDrawnException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidEndedException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidStartAtException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidStartedException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketAllocationException;
use App\RaffleDemo\Raffle\Domain\ValueObject\CloseAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Closed;
use App\RaffleDemo\Raffle\Domain\ValueObject\Created;
use App\RaffleDemo\Raffle\Domain\ValueObject\DrawAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Drawn;
use App\RaffleDemo\Raffle\Domain\ValueObject\Ended;
use App\RaffleDemo\Raffle\Domain\ValueObject\Name;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Prize;
use App\RaffleDemo\Raffle\Domain\ValueObject\StartAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Started;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketPrice;
use App\RaffleDemo\Raffle\Domain\ValueObject\TotalTickets;
use App\RaffleDemo\Raffle\Domain\ValueObject\Winner;

final class Raffle extends AbstractAggregate
{
    public const string AGGREGATE_NAME = 'raffle';

    private RaffleAggregateId $id;

    public Name $name;
    public Prize $prize;
    public StartAt $startAt;

    public CloseAt $closeAt;
    public DrawAt $drawAt;

    public TotalTickets $totalTickets;

    public TicketPrice $ticketPrice;
    public Created $created;

    public TicketAllocations $ticketAllocations;

    public ?Started $started = null;
    public ?Closed $closed = null;
    public ?Drawn $drawn = null;
    public ?Winner $winner = null;

    public ?Ended $ended = null;

    public function __construct()
    {
        $this->events = AggregateEvents::fromNew();
        $this->version = RaffleAggregateVersion::fromNew();
        $this->ticketAllocations = TicketAllocations::fromNew();
    }

    public function getAggregateName(): RaffleAggregateName
    {
        return RaffleAggregateName::fromString(self::AGGREGATE_NAME);
    }

    public function getAggregateId(): RaffleAggregateId
    {
        return $this->id;
    }

    public function getAggregateVersion(): RaffleAggregateVersion
    {
        return $this->version; // @phpstan-ignore-line return.type
    }

    public static function create(
        RaffleAggregateId $id,
        Name $name,
        Prize $prize,
        StartAt $startAt,
        CloseAt $closeAt,
        DrawAt $drawAt,
        TotalTickets $totalTickets,
        TicketPrice $ticketPrice,
        Created $created,
    ): self {
        if ($created->at > $startAt->toDateTime()) {
            throw InvalidCreatedException::fromCreatedAtAfterStartAt();
        }

        /* @infection-ignore-all */
        if ($startAt->toDateTime() >= $closeAt->toDateTime() || $startAt->toDateTime()->diff($closeAt->toDateTime())->days < 1) {
            throw InvalidStartAtException::fromStartAtLessThan1DayBeforeCloseAt();
        }

        if ($closeAt->toDateTime() > $drawAt->toDateTime()) {
            throw InvalidClosedAtException::fromCloseAtAfterDrawAt();
        }

        $raffle = new self();

        $raffle->raise(
            new Event\RaffleCreated(
                aggregateVersion: $raffle->getAggregateVersion(),
                aggregateId: $id,
                name: $name,
                prize: $prize,
                startAt: $startAt,
                closeAt: $closeAt,
                drawAt: $drawAt,
                totalTickets: $totalTickets,
                ticketPrice: $ticketPrice,
                created: $created,
                occurredAt: OccurredAt::fromNow(),
            ),
        );

        return $raffle;
    }

    public function start(
        Started $started,
    ): void {
        if ($started->at < $this->startAt->toDateTime()) {
            throw InvalidStartedException::fromCannotStartBeforeStartAtDate();
        }

        if ($this->started !== null) {
            throw InvalidStartedException::fromAlreadyStarted();
        }

        $this->raise(
            new Event\RaffleStarted(
                aggregateVersion: $this->getAggregateVersion(),
                aggregateId: $this->id,
                started: $started,
                occurredAt: OccurredAt::fromNow(),
            ),
        );
    }

    public function allocateTicketToParticipant(
        TicketAllocation $ticketAllocation,
    ): void {
        if ($this->started === null || $this->started->at > $ticketAllocation->allocatedAt) {
            throw InvalidTicketAllocationException::fromCannotAllocateBeforeStarted();
        }

        if ($this->closed !== null || $this->closeAt->toDateTime() < $ticketAllocation->allocatedAt) {
            throw InvalidTicketAllocationException::fromCannotAllocateAfterClosed();
        }

        if ($this->ticketAllocations->has($ticketAllocation)) {
            throw InvalidTicketAllocationException::fromDuplicateTicketAllocation();
        }

        if (($this->ticketAllocations->numberOfTicketsAllocated + $ticketAllocation->quantity) > $this->totalTickets->toInt()) {
            throw InvalidTicketAllocationException::fromOverAllocationOfTickets();
        }

        $this->raise(
            new Event\TicketAllocatedToParticipant(
                aggregateVersion: $this->getAggregateVersion(),
                aggregateId: $this->id,
                ticketAllocation: $ticketAllocation,
                occurredAt: OccurredAt::fromNow(),
            ),
        );
    }

    public function close(
        Closed $closed,
    ): void {
        if ($this->closed !== null) {
            throw InvalidClosedException::fromAlreadyClosed();
        }

        $this->raise(
            new Event\RaffleClosed(
                aggregateVersion: $this->getAggregateVersion(),
                aggregateId: $this->id,
                closed: $closed,
                occurredAt: OccurredAt::fromNow(),
            ),
        );

        if ($this->ticketAllocations->numberOfTicketsAllocated === 0) {
            $this->end(
                Ended::from(
                    by: $closed->by,
                    at: $closed->at,
                    reason: 'Automatically ended due to closed raffle without any ticket allocations',
                ),
            );
        }
    }

    public function drawPrize(
        Drawn $drawn,
    ): void {
        if ($this->closed === null) {
            throw InvalidDrawnException::fromCannotDrawPrizeBeforeClosed();
        }

        if ($this->drawn !== null) {
            throw InvalidDrawnException::fromAlreadyDrawn();
        }

        if ($this->ticketAllocations->numberOfTicketsAllocated === 0) {
            throw InvalidDrawnException::fromCannotDrawWhenNoTicketAllocations();
        }

        $winningTicketNumber = $this->ticketAllocations->drawWinningTicketNumber();

        $winningTicketAllocation = $this->ticketAllocations->drawTicketAllocationFromTicketNumber($winningTicketNumber);

        $this->raise(
            new Event\PrizeDrawn(
                aggregateVersion: $this->getAggregateVersion(),
                aggregateId: $this->id,
                drawn: $drawn,
                winner: Winner::fromWinningTicketAllocation($winningTicketAllocation, $winningTicketNumber),
                occurredAt: OccurredAt::fromNow(),
            ),
        );

        $this->end(
            Ended::from(
                by: $drawn->by,
                at: $drawn->at,
                reason: 'Automatically ended due to drawn raffle and winner selected',
            ),
        );
    }

    public function end(Ended $ended): void
    {
        if ($this->closed === null) {
            throw InvalidEndedException::fromCannotEndBeforeClosed();
        }

        if ($this->ended !== null) {
            throw InvalidEndedException::fromAlreadyEnded();
        }

        $this->raise(
            new Event\RaffleEnded(
                aggregateVersion: $this->getAggregateVersion(),
                aggregateId: $this->id,
                ended: $ended,
                occurredAt: OccurredAt::fromNow(),
            ),
        );
    }

    public function apply(AggregateEventInterface $event): void
    {
        match ($event::class) {
            Event\RaffleCreated::class => $this->applyRaffleCreated($event),
            Event\RaffleStarted::class => $this->applyRaffleStarted($event),
            Event\TicketAllocatedToParticipant::class => $this->applyTicketAllocatedToParticipant($event),
            Event\RaffleClosed::class => $this->applyRaffleClosed($event),
            Event\PrizeDrawn::class => $this->applyPrizeDrawn($event),
            Event\RaffleEnded::class => $this->applyRaffleEnded($event),
            default => throw AggregateEventNotHandledException::notHandledByAggregate($event::class, self::class)
        };
    }

    private function applyRaffleCreated(Event\RaffleCreated $event): void
    {
        $this->id = $event->getAggregateId();
        $this->name = $event->name;
        $this->prize = $event->prize;
        $this->startAt = $event->startAt;
        $this->closeAt = $event->closeAt;
        $this->drawAt = $event->drawAt;
        $this->totalTickets = $event->totalTickets;
        $this->ticketPrice = $event->ticketPrice;
        $this->created = $event->created;
    }

    private function applyRaffleStarted(Event\RaffleStarted $event): void
    {
        $this->started = $event->started;
    }

    private function applyTicketAllocatedToParticipant(Event\TicketAllocatedToParticipant $event): void
    {
        $this->ticketAllocations->addTicketAllocation($event->ticketAllocation);
    }

    private function applyRaffleClosed(Event\RaffleClosed $event): void
    {
        $this->closed = $event->closed;
    }

    private function applyPrizeDrawn(Event\PrizeDrawn $event): void
    {
        $this->drawn = $event->drawn;
        $this->winner = $event->winner;
    }

    private function applyRaffleEnded(Event\RaffleEnded $event): void
    {
        $this->ended = $event->ended;
    }
}

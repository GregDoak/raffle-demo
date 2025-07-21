<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Repository;

use App\Framework\Domain\Repository\EventStoreInterface;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateName;

final readonly class RaffleEventStoreRepository
{
    public function __construct(
        private EventStoreInterface $eventStore,
        // TODO - add aggregate bus
    ) {
    }

    public function store(Raffle $raffle): void
    {
        $events = $raffle->flushEvents();

        $this->eventStore->store($events);
        // TODO - dispatch aggregate events on bus
    }

    public function get(RaffleAggregateId $id): Raffle
    {
        $events = $this->eventStore->get(RaffleAggregateName::fromString(Raffle::AGGREGATE_NAME), $id);

        return Raffle::buildFrom($events);
    }
}

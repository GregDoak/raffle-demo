<?php

declare(strict_types=1);

namespace App\Tests\Double\Framework\Domain\Repository;

use App\Framework\Domain\Model\AbstractAggregateId;
use App\Framework\Domain\Model\AbstractAggregateName;
use App\Framework\Domain\Model\AggregateEvents;
use App\Framework\Domain\Repository\EventStoreInterface;

final class InMemoryEventStore implements EventStoreInterface
{
    private AggregateEvents $events;

    public function __construct()
    {
        $this->events = AggregateEvents::fromNew();
    }

    public function store(AggregateEvents $events): void
    {
        foreach ($events as $event) {
            $this->events = $this->events->add($event);
        }
    }

    public function get(AbstractAggregateName $name, AbstractAggregateId $id): AggregateEvents
    {
        $events = AggregateEvents::fromNew();

        foreach ($this->events as $event) {
            if ($event->getAggregateName()->equals($name) && $event->getAggregateId()->equals($id)) {
                $events = $events->add($event);
            }
        }

        return $events;
    }
}

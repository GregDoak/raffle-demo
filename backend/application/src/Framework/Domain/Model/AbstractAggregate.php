<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model;

use App\Framework\Domain\Model\Event\AggregateEventInterface;

abstract class AbstractAggregate
{
    protected AggregateEvents $events;
    protected AggregateVersionInterface $version;

    abstract public function __construct();

    abstract public function getAggregateName(): AggregateNameInterface;

    abstract public function getAggregateId(): AggregateIdInterface;

    abstract public function getAggregateVersion(): AggregateVersionInterface;

    public function countOfEvents(): int
    {
        return $this->events->count();
    }

    public function flushEvents(): AggregateEvents
    {
        $events = $this->events;

        $this->events = AggregateEvents::fromNew();

        return $events;
    }

    public static function buildFrom(AggregateEvents $events): static
    {
        $aggregate = new static();

        foreach ($events as $event) {
            $aggregate->apply($event);
        }

        return $aggregate;
    }

    protected function raise(AggregateEventInterface $event): void
    {
        $this->apply($event);

        $this->version = $this->version->next();

        $this->events = $this->events->add($event);
    }

    abstract public function apply(AggregateEventInterface $event): void;
}

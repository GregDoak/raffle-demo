<?php

declare(strict_types=1);

namespace App\Tests\Double\Framework\Domain\Model\Event;

use App\Framework\Domain\Model\AggregateEvents;
use App\Framework\Domain\Model\Event\AggregateEventsBusInterface;

final class AggregateEventsBusSpy implements AggregateEventsBusInterface
{
    public private(set) AggregateEvents $events {
        get {
            return $this->events;
        }
    }

    public function __construct()
    {
        $this->events = AggregateEvents::fromNew();
    }

    public function publish(AggregateEvents $events): void
    {
        $this->events = $events;
    }
}

<?php

declare(strict_types=1);

namespace App\Framework\Domain\Repository;

use App\Framework\Domain\Model\AbstractAggregateId;
use App\Framework\Domain\Model\AbstractAggregateName;
use App\Framework\Domain\Model\AggregateEvents;

interface EventStoreInterface
{
    public function store(AggregateEvents $events): void;

    public function get(AbstractAggregateName $name, AbstractAggregateId $id): AggregateEvents;
}

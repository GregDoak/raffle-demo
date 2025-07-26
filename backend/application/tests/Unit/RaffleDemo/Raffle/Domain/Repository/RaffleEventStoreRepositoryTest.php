<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Repository;

use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Model\RaffleDomainContext;
use App\Tests\Double\Framework\Domain\Model\Event\AggregateEventsBusSpy;
use App\Tests\Double\Framework\Domain\Repository\InMemoryEventStore;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleEventStoreRepositoryTest extends TestCase
{
    #[Test]
    public function it_succeeds_to_persist_the_aggregate_and_dispatch_aggregate_events(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $repository = new RaffleEventStoreRepository(
            $eventStore = new InMemoryEventStore(),
            $aggregateEventsBus = new AggregateEventsBusSpy(),
        );

        // Act
        $repository->store($raffle);

        // Assert
        $eventStoreEvents = $eventStore->get($raffle->getAggregateName(), $raffle->getAggregateId());
        $aggregateEvents = $aggregateEventsBus->events;

        self::assertCount(1, $eventStoreEvents);
        self::assertCount(1, $aggregateEvents);
        self::assertSame($eventStoreEvents->toArray(), $aggregateEvents->toArray());
    }
}

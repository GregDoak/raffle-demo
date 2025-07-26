<?php

declare(strict_types=1);

namespace App\Tests\Integration\RaffleDemo\Raffle\Infrastructure\Postgres\Repository;

use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Infrastructure\Postgres\Repository\PostgresRaffleEventStore;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Model\RaffleDomainContext;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Framework\Attributes\Test;

final class PostgresRaffleEventStoreTest extends AbstractIntegrationTestCase
{
    private PostgresRaffleEventStore $eventStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventStore = self::getContainer()->get(PostgresRaffleEventStore::class);
    }

    #[Test]
    public function it_stores_and_hydrates_the_aggregate(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();

        // Act
        $this->eventStore->store($raffle->flushEvents());
        $hydratedAggregate = Raffle::buildFrom(
            $this->eventStore->get($raffle->getAggregateName(), $raffle->getAggregateId()),
        );

        // Assert
        self::assertEquals($raffle, $hydratedAggregate);
    }

    #[Test]
    public function it_fails_to_store_when_aggregate_with_duplicate_version_already_exists(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $events = $raffle->flushEvents();
        $this->eventStore->store($events);

        // Act
        self::expectException(UniqueConstraintViolationException::class);

        $this->eventStore->store($events);

        // Assert
        self::fail();
    }
}

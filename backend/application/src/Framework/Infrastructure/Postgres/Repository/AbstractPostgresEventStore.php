<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Postgres\Repository;

use App\Framework\Domain\Model\AbstractAggregateId;
use App\Framework\Domain\Model\AbstractAggregateName;
use App\Framework\Domain\Model\AggregateEvents;
use App\Framework\Domain\Model\Event\AggregateEventFactoryInterface;
use App\Framework\Domain\Model\Event\AggregateEventInterface;
use App\Framework\Domain\Repository\EventStoreInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

abstract readonly class AbstractPostgresEventStore implements EventStoreInterface
{
    final public function __construct(
        private Connection $connection,
        private AggregateEventFactoryInterface $aggregateEventFactory,
        private string $domain,
    ) {
    }

    public function store(AggregateEvents $events): void
    {
        $this->connection->beginTransaction();

        $sql = <<<SQL
            INSERT INTO {$this->domain}.event_store
                (aggregate_name, aggregate_id, aggregate_version, event_name, event_data)
            VALUES
                (:aggregate_name, :aggregate_id, :aggregate_version, :event_name, :event_data);
        SQL;

        $statement = $this->connection->prepare($sql);

        /** @var AggregateEventInterface $event */
        foreach ($events as $event) {
            $statement->bindValue('aggregate_name', $event->getAggregateName()->toString());
            $statement->bindValue('aggregate_id', $event->getAggregateId()->toString());
            $statement->bindValue('aggregate_version', $event->getAggregateVersion()->toInt(), ParameterType::INTEGER);
            $statement->bindValue('event_name', $event->getEventName());
            $statement->bindValue('event_data', $event->serialize());

            $statement->executeStatement();
        }

        $this->connection->commit();
    }

    public function get(AbstractAggregateName $name, AbstractAggregateId $id): AggregateEvents
    {
        $sql = <<<SQL
            SELECT
                event_store.event_name,
                event_store.event_data
            FROM
                {$this->domain}.event_store
            WHERE
                event_store.aggregate_name = :aggregate_name
                AND event_store.aggregate_id = :aggregate_id
            ORDER BY
                event_store.aggregate_version ASC;
        SQL;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('aggregate_name', $name->toString());
        $statement->bindValue('aggregate_id', $id->toString());

        $result = $statement->executeQuery();

        return array_reduce(
            $result->fetchAllAssociative(),
            fn (AggregateEvents $events, array $event) => $events->add(
                $this->aggregateEventFactory->fromSerialized(
                    $event['event_name'], // @phpstan-ignore-line argument.type
                    $event['event_data'], // @phpstan-ignore-line argument.type
                ),
            ),
            AggregateEvents::fromNew(),
        );
    }
}

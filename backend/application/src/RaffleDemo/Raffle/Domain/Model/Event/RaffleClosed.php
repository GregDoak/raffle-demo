<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Model\Event;

use App\Foundation\Serializer\JsonSerializer;
use App\Framework\Domain\Model\Event\AggregateEventInterface;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateName;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateVersion;
use App\RaffleDemo\Raffle\Domain\ValueObject\Closed;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;

final readonly class RaffleClosed implements AggregateEventInterface
{
    public const string EVENT_NAME = 'raffle.closed';

    public function __construct(
        private RaffleAggregateVersion $aggregateVersion,
        private RaffleAggregateId $aggregateId,
        public Closed $closed,
        public OccurredAt $occurredAt,
    ) {
    }

    public function getEventName(): string
    {
        return self::EVENT_NAME;
    }

    public function getAggregateName(): RaffleAggregateName
    {
        return RaffleAggregateName::fromString(Raffle::AGGREGATE_NAME);
    }

    public function getAggregateId(): RaffleAggregateId
    {
        return $this->aggregateId;
    }

    public function getAggregateVersion(): RaffleAggregateVersion
    {
        return $this->aggregateVersion;
    }

    public function serialize(): string
    {
        return JsonSerializer::serialize([
            'aggregateVersion' => $this->aggregateVersion->toInt(),
            'aggregateId' => $this->aggregateId->toString(),
            'closed' => $this->closed->toArray(),
            'occurredAt' => $this->occurredAt->toString(),
        ]);
    }

    public static function deserialize(string $serialized): AggregateEventInterface
    {
        /** @var array{
         *     aggregateVersion: int,
         *     aggregateId: string,
         *     closed: array{by: string, at:string},
         *     occurredAt: string
         * } $event
         */
        $event = JsonSerializer::deserialize($serialized);

        return new self(
            RaffleAggregateVersion::fromInt($event['aggregateVersion']),
            RaffleAggregateId::fromString($event['aggregateId']),
            Closed::fromArray($event['closed']),
            OccurredAt::fromString($event['occurredAt']),
        );
    }
}

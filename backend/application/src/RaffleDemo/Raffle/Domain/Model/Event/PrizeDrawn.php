<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Model\Event;

use App\Foundation\Serializer\JsonSerializer;
use App\Framework\Domain\Model\Event\AggregateEventInterface;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateName;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateVersion;
use App\RaffleDemo\Raffle\Domain\ValueObject\Drawn;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Winner;

final readonly class PrizeDrawn implements AggregateEventInterface
{
    public const string EVENT_NAME = 'raffle.prize_drawn';

    public function __construct(
        private RaffleAggregateVersion $aggregateVersion,
        private RaffleAggregateId $aggregateId,
        public Drawn $drawn,
        public Winner $winner,
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
            'drawn' => $this->drawn->toArray(),
            'winner' => $this->winner->toArray(),
            'occurredAt' => $this->occurredAt->toString(),
        ]);
    }

    public static function deserialize(string $serialized): AggregateEventInterface
    {
        /** @var array{
         *     aggregateVersion: int,
         *     aggregateId: string,
         *     drawn: array{by: string, at:string},
         *     winner: array{ticketAllocation: array{quantity: int, allocatedTo: string, allocatedAt: string}, ticketNumber:int},
         *     occurredAt: string
         * } $event
         */
        $event = JsonSerializer::deserialize($serialized);

        return new self(
            RaffleAggregateVersion::fromInt($event['aggregateVersion']),
            RaffleAggregateId::fromString($event['aggregateId']),
            Drawn::fromArray($event['drawn']),
            Winner::fromArray($event['winner']),
            OccurredAt::fromString($event['occurredAt']),
        );
    }
}

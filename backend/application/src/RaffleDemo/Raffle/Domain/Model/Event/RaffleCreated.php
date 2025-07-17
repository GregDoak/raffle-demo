<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Model\Event;

use App\Foundation\Serializer\JsonSerializer;
use App\Framework\Domain\Model\Event\AggregateEventInterface;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateName;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateVersion;
use App\RaffleDemo\Raffle\Domain\ValueObject\CloseAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Created;
use App\RaffleDemo\Raffle\Domain\ValueObject\DrawAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Name;
use App\RaffleDemo\Raffle\Domain\ValueObject\OccurredAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\Prize;
use App\RaffleDemo\Raffle\Domain\ValueObject\StartAt;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketPrice;
use App\RaffleDemo\Raffle\Domain\ValueObject\TotalTickets;

final readonly class RaffleCreated implements AggregateEventInterface
{
    public const string EVENT_NAME = 'raffle.created';

    public function __construct(
        private RaffleAggregateVersion $aggregateVersion,
        private RaffleAggregateId $aggregateId,
        public Name $name,
        public Prize $prize,
        public StartAt $startAt,
        public CloseAt $closeAt,
        public DrawAt $drawAt,
        public TotalTickets $totalTickets,
        public TicketPrice $ticketPrice,
        public Created $created,
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
            'name' => $this->name->toString(),
            'prize' => $this->prize->toString(),
            'startAt' => $this->startAt->toString(),
            'closeAt' => $this->closeAt->toString(),
            'drawAt' => $this->drawAt->toString(),
            'totalTickets' => $this->totalTickets->toInt(),
            'ticketPrice' => $this->ticketPrice->toArray(),
            'created' => $this->created->toArray(),
            'occurredAt' => $this->occurredAt->toString(),
        ]);
    }

    public static function deserialize(string $serialized): AggregateEventInterface
    {
        /** @var array{
         *     aggregateVersion: int,
         *     aggregateId: string,
         *     name: string,
         *     prize: string,
         *     startAt: string,
         *     closeAt: string,
         *     drawAt: string,
         *     totalTickets: int,
         *     ticketPrice: array{amount: int, currency:string},
         *     created: array{by: string, at:string},
         *     occurredAt: string
         * } $event
         */
        $event = JsonSerializer::deserialize($serialized);

        return new self(
            RaffleAggregateVersion::fromInt($event['aggregateVersion']),
            RaffleAggregateId::fromString($event['aggregateId']),
            Name::fromString($event['name']),
            Prize::fromString($event['prize']),
            StartAt::fromString($event['startAt']),
            CloseAt::fromString($event['closeAt']),
            DrawAt::fromString($event['drawAt']),
            TotalTickets::fromInt($event['totalTickets']),
            TicketPrice::fromArray($event['ticketPrice']),
            Created::fromArray($event['created']),
            OccurredAt::fromString($event['occurredAt']),
        );
    }
}

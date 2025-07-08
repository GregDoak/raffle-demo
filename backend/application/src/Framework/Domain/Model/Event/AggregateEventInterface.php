<?php

declare(strict_types=1);

namespace App\Framework\Domain\Model\Event;

use App\Framework\Domain\Model\AggregateIdInterface;
use App\Framework\Domain\Model\AggregateNameInterface;
use App\Framework\Domain\Model\AggregateVersionInterface;

interface AggregateEventInterface
{
    public function getEventName(): string;

    public function getAggregateName(): AggregateNameInterface;

    public function getAggregateId(): AggregateIdInterface;

    public function getAggregateVersion(): AggregateVersionInterface;

    public function serialize(): string;

    public static function deserialize(string $serialized): self;
}

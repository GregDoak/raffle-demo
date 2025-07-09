<?php

declare(strict_types=1);

namespace App\Framework\Domain\Event;

interface DomainEventBusInterface
{
    public function publish(DomainEventInterface $event): void;
}

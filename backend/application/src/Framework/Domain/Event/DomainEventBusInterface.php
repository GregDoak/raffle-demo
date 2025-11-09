<?php

declare(strict_types=1);

namespace App\Framework\Domain\Event;

use App\Foundation\DomainEventRegistry\DomainEventInterface;

interface DomainEventBusInterface
{
    public function publish(DomainEventInterface $event): void;
}

<?php

declare(strict_types=1);

namespace App\Application\Mono\Framework\Domain\Event;

interface DomainEventBusInterface
{
    public function publish(DomainEventInterface $event): void;
}

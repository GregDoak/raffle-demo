<?php

declare(strict_types=1);

namespace App\Framework\Domain\Event;

interface DomainEventInterface
{
    public function serialize(): string;
}

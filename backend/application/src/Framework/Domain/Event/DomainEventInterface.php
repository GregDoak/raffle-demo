<?php

declare(strict_types=1);

namespace App\Application\Mono\Framework\Domain\Event;

interface DomainEventInterface
{
    public function serialize(): string;
}

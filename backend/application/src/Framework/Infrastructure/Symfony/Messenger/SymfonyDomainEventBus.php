<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger;

use App\Foundation\DomainEventRegistry\DomainEventInterface;
use App\Framework\Domain\Event\DomainEventBusInterface;
use App\Framework\Infrastructure\Symfony\Messenger\Middleware\SendToPublisherStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

final readonly class SymfonyDomainEventBus implements DomainEventBusInterface
{
    public function __construct(
        private MessageBusInterface $domainEventBus,
    ) {
    }

    public function publish(DomainEventInterface $event): void
    {
        $this->domainEventBus->dispatch($event, [new TransportMessageIdStamp($event->getEventId()), new SendToPublisherStamp()]);
    }
}

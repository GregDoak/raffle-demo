<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\UserInterface\Event\RaffleStartedV1;

use App\Foundation\DomainEventRegistry\Raffle\RaffleStartedV1Event;
use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\UserInterface\Event\DomainEventSubscriberInterface;
use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\RaffleStartedV1EmailNotification;
use App\RaffleDemo\Notification\Application\Command\SendNotification\SendNotificationCommand;

final readonly class RaffleStartedV1EventSubscriber implements DomainEventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(RaffleStartedV1Event $event): void
    {
        $command = SendNotificationCommand::create(RaffleStartedV1EmailNotification::fromEvent($event));

        $this->commandBus->dispatchSync($command);
    }
}

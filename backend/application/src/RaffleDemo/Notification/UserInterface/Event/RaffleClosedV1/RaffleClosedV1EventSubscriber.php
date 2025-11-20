<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\UserInterface\Event\RaffleClosedV1;

use App\Foundation\DomainEventRegistry\Raffle\RaffleClosedV1Event;
use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\UserInterface\Event\DomainEventSubscriberInterface;
use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\RaffleClosedV1EmailNotification;
use App\RaffleDemo\Notification\Application\Command\SendNotification\SendNotificationCommand;

final readonly class RaffleClosedV1EventSubscriber implements DomainEventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(RaffleClosedV1Event $event): void
    {
        $command = SendNotificationCommand::create(RaffleClosedV1EmailNotification::fromEvent($event));

        $this->commandBus->dispatchSync($command);
    }
}

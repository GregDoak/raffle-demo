<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\UserInterface\Event\RaffleCreatedV1;

use App\Foundation\DomainEventRegistry\Raffle\RaffleCreatedV1Event;
use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\UserInterface\Event\DomainEventSubscriberInterface;
use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\RaffleCreatedV1EmailNotification;
use App\RaffleDemo\Notification\Application\Command\SendNotification\SendNotificationCommand;

final readonly class RaffleCreatedV1EventSubscriber implements DomainEventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(RaffleCreatedV1Event $event): void
    {
        $command = SendNotificationCommand::create(RaffleCreatedV1EmailNotification::fromEvent($event));

        $this->commandBus->dispatchSync($command);
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\UserInterface\Event\RaffleEndedV1Event;

use App\Foundation\DomainEventRegistry\Raffle\RaffleEndedV1Event;
use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\UserInterface\Event\DomainEventSubscriberInterface;
use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\RaffleEndedV1EmailNotification;
use App\RaffleDemo\Notification\Application\Command\SendNotification\SendNotificationCommand;

final readonly class RaffleEndedV1EventSubscriber implements DomainEventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(RaffleEndedV1Event $event): void
    {
        $command = SendNotificationCommand::create(RaffleEndedV1EmailNotification::fromEvent($event));

        $this->commandBus->dispatchSync($command);
    }
}

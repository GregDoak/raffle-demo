<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\UserInterface\Event\RaffleDrawnV1Event;

use App\Foundation\DomainEventRegistry\Raffle\RaffleDrawnV1Event;
use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\UserInterface\Event\DomainEventSubscriberInterface;
use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\RaffleDrawnV1WinnerEmailNotification;
use App\RaffleDemo\Notification\Application\Command\SendNotification\SendNotificationCommand;

final readonly class RaffleDrawnV1WinnerEventSubscriber implements DomainEventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(RaffleDrawnV1Event $event): void
    {
        $command = SendNotificationCommand::create(RaffleDrawnV1WinnerEmailNotification::fromEvent($event));

        $this->commandBus->dispatchSync($command);
    }
}

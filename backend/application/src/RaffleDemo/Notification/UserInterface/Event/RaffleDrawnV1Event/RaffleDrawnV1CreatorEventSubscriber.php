<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\UserInterface\Event\RaffleDrawnV1Event;

use App\Foundation\DomainEventRegistry\Raffle\RaffleDrawnV1Event;
use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\UserInterface\Event\DomainEventSubscriberInterface;
use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\RaffleDrawnV1CreatorEmailNotification;
use App\RaffleDemo\Notification\Application\Command\SendNotification\SendNotificationCommand;

final readonly class RaffleDrawnV1CreatorEventSubscriber implements DomainEventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(RaffleDrawnV1Event $event): void
    {
        $command = SendNotificationCommand::create(RaffleDrawnV1CreatorEmailNotification::fromEvent($event));

        $this->commandBus->dispatchSync($command);
    }
}

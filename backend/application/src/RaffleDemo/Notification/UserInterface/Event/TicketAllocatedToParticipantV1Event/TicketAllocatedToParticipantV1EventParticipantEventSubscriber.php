<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\UserInterface\Event\TicketAllocatedToParticipantV1Event;

use App\Foundation\DomainEventRegistry\Raffle\TicketAllocatedToParticipantV1Event;
use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\UserInterface\Event\DomainEventSubscriberInterface;
use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\TicketAllocatedToParticipantV1EventParticipantEmailNotification;
use App\RaffleDemo\Notification\Application\Command\SendNotification\SendNotificationCommand;

final readonly class TicketAllocatedToParticipantV1EventParticipantEventSubscriber implements DomainEventSubscriberInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(TicketAllocatedToParticipantV1Event $event): void
    {
        $command = SendNotificationCommand::create(TicketAllocatedToParticipantV1EventParticipantEmailNotification::fromEvent($event));

        $this->commandBus->dispatchSync($command);
    }
}

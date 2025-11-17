<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Service\Notifier;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;
use App\RaffleDemo\Notification\Application\Service\Sender\SenderInterface;

final readonly class Notifier implements NotifierInterface
{
    /** @param SenderInterface[] $senders */
    public function __construct(
        private iterable $senders,
    ) {
    }

    public function notify(NotificationInterface $notification): void
    {
        foreach ($this->senders as $sender) {
            if ($sender->supports($notification)) {
                $sender->send($notification);

                return;
            }
        }

        throw UnsupportedNotificationException::fromNotification($notification);
    }
}

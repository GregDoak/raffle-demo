<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Command\SendNotification;

use App\Framework\Application\Command\CommandInterface;
use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;

/** @infection-ignore-all */
final readonly class SendNotificationCommand implements CommandInterface
{
    private function __construct(
        public NotificationInterface $notification,
    ) {
    }

    public static function create(NotificationInterface $notification): self
    {
        return new self($notification);
    }
}

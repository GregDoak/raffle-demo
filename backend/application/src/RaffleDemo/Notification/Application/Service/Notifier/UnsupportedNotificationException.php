<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Service\Notifier;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;
use RuntimeException;

use function sprintf;

final class UnsupportedNotificationException extends RuntimeException
{
    public static function fromNotification(NotificationInterface $notification): self
    {
        return new self(
            sprintf(
                'Unable to locate a sender for channel "%s" for the notification %s',
                $notification->getChannel()->value,
                $notification::class,
            ),
        );
    }
}

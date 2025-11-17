<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Service\Notifier;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;

interface NotifierInterface
{
    public function notify(NotificationInterface $notification): void;
}

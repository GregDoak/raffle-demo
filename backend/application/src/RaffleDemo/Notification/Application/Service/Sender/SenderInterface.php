<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Service\Sender;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;

interface SenderInterface
{
    public function supports(NotificationInterface $notification): bool;

    public function send(NotificationInterface $notification): void;
}

<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Notification\Infrastructure\Sender;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;
use App\RaffleDemo\Notification\Application\Service\Sender\SenderInterface;

abstract class AbstractSenderSpy implements SenderInterface
{
    /** @var NotificationInterface[] */
    public array $notifications = [];

    abstract public function supports(NotificationInterface $notification): bool;

    public function send(NotificationInterface $notification): void
    {
        $this->notifications[$notification->getId()->toString()] = $notification;
    }
}

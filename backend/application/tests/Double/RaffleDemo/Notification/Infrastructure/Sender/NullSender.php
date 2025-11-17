<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Notification\Infrastructure\Sender;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;
use App\RaffleDemo\Notification\Application\Service\Sender\SenderInterface;

final readonly class NullSender implements SenderInterface
{
    public function supports(NotificationInterface $notification): bool
    {
        return true;
    }

    public function send(NotificationInterface $notification): void
    {
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Notification\Infrastructure\Sender;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;

final class EmailSenderSpy extends AbstractSenderSpy
{
    public function supports(NotificationInterface $notification): bool
    {
        return $notification->getChannel() === Channel::EMAIL; // @phpstan-ignore-line identical.alwaysTrue
    }
}

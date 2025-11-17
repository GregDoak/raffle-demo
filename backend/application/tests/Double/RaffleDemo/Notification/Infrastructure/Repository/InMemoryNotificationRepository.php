<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Notification\Infrastructure\Repository;

use App\RaffleDemo\Notification\Domain\Exception\InvalidNotificationException;
use App\RaffleDemo\Notification\Domain\Model\Notification;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\Repository\NotificationRepositoryInterface;
use App\RaffleDemo\Notification\Domain\ValueObject\AbstractRecipient;

use function array_key_exists;

final class InMemoryNotificationRepository implements NotificationRepositoryInterface
{
    /** @var Notification[] */
    public array $notifications = [];

    public function getById(NotificationId $id): ?Notification
    {
        if (array_key_exists($id->toString(), $this->notifications)) {
            return $this->notifications[$id->toString()];
        }

        return null;
    }

    public function getByRecipient(AbstractRecipient $recipient): array
    {
        return array_filter(
            $this->notifications,
            fn (Notification $notification) => $notification->recipient->toString() === $recipient->toString(),
        );
    }

    /** @throws InvalidNotificationException */
    public function store(Notification $notification): void
    {
        if (array_key_exists($notification->id->toString(), $this->notifications)) {
            throw InvalidNotificationException::fromDuplicateNotification();
        }

        $this->notifications[$notification->id->toString()] = $notification;
    }
}

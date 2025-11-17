<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\Repository;

use App\RaffleDemo\Notification\Domain\Exception\InvalidNotificationException;
use App\RaffleDemo\Notification\Domain\Model\Notification;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\AbstractRecipient;

interface NotificationRepositoryInterface
{
    public function getById(NotificationId $id): ?Notification;

    /** @return Notification[] */
    public function getByRecipient(AbstractRecipient $recipient): array;

    /** @throws InvalidNotificationException */
    public function store(Notification $notification): void;
}

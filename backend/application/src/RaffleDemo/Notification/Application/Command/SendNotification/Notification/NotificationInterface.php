<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Command\SendNotification\Notification;

use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\AbstractRecipient;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;

interface NotificationInterface
{
    public function getId(): NotificationId;

    public function getType(): Type;

    public function getChannel(): Channel;

    public function getRecipient(): AbstractRecipient;

    public function getSender(): AbstractRecipient;

    public function getSubject(): Subject;

    public function getBody(): Body;

    public function getCcRecipients(): RecipientCollection;

    public function getBccRecipients(): RecipientCollection;
}

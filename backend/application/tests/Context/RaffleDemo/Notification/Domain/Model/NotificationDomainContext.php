<?php

declare(strict_types=1);

namespace App\Tests\Context\RaffleDemo\Notification\Domain\Model;

use App\RaffleDemo\Notification\Domain\Model\Notification;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\AbstractRecipient;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Status;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;

final readonly class NotificationDomainContext
{
    public static function create(
        ?NotificationId $notificationId = null,
        ?Type $type = null,
        ?Channel $channel = null,
        ?AbstractRecipient $recipient = null,
        ?RecipientCollection $ccRecipients = null,
        ?RecipientCollection $bccRecipients = null,
        ?AbstractRecipient $sender = null,
        ?Subject $subject = null,
        ?Body $body = null,
        ?Status $status = null,
    ): Notification {
        return Notification::create(
            id: $notificationId ?? NotificationId::fromNew(),
            type: $type ?? Type::fromString('notification.dummy.v1'),
            channel: $channel ?? Channel::EMAIL,
            recipient: $recipient ?? EmailAddress::fromString('recipient@example.com'),
            ccRecipients: $ccRecipients ?? RecipientCollection::fromArray(
                EmailAddress::fromString('cc.recipient.1@example.com'),
                EmailAddress::fromString('cc.recipient.2@example.com'),
            ),
            bccRecipients: $bccRecipients ?? RecipientCollection::fromArray(
                EmailAddress::fromString('bcc.recipient.1@example.com'),
                EmailAddress::fromString('bcc.recipient.2@example.com'),
            ),
            sender: $sender ?? EmailAddress::fromString('sender@example.com'),
            subject: $subject ?? Subject::fromString('subject'),
            body: $body ?? Body::fromString('body'),
            status: $status ?? Status::SENT,
        );
    }
}

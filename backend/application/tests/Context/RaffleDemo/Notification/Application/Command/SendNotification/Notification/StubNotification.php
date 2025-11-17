<?php

declare(strict_types=1);

namespace App\Tests\Context\RaffleDemo\Notification\Application\Command\SendNotification\Notification;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;

final readonly class StubNotification implements NotificationInterface
{
    private function __construct(
        private NotificationId $id,
        private Type $type,
        private Channel $channel,
        private EmailAddress $recipient,
        private EmailAddress $sender,
        private Subject $subject,
        private Body $body,
        private RecipientCollection $ccRecipients,
        private RecipientCollection $bccRecipients,
    ) {
    }

    public static function create(
        ?NotificationId $id = null,
        ?Type $type = null,
        ?Channel $channel = null,
        ?EmailAddress $recipient = null,
        ?EmailAddress $sender = null,
        ?Subject $subject = null,
        ?Body $body = null,
        ?RecipientCollection $ccRecipients = null,
        ?RecipientCollection $bccRecipients = null,
    ): self {
        return new self(
            id: $id ?? NotificationId::fromNew(),
            type: $type ?? Type::fromString('notification.stub'),
            channel: $channel ?? Channel::EMAIL,
            recipient: $recipient ?? EmailAddress::fromString('recipient@example.com'),
            sender: $sender ?? EmailAddress::fromString('sender@example.com'),
            subject: $subject ?? Subject::fromString('subject'),
            body: $body ?? Body::fromString('body'),
            ccRecipients: $ccRecipients ?? RecipientCollection::fromArray(
                EmailAddress::fromString('cc.recipient.1@example.com'),
                EmailAddress::fromString('cc.recipient.2@example.com'),
            ),
            bccRecipients: $bccRecipients ?? RecipientCollection::fromArray(
                EmailAddress::fromString('bcc.recipient.1@example.com'),
                EmailAddress::fromString('bcc.recipient.2@example.com'),
            ),
        );
    }

    public function getId(): NotificationId
    {
        return $this->id;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function getRecipient(): EmailAddress
    {
        return $this->recipient;
    }

    public function getSender(): EmailAddress
    {
        return $this->sender;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function getBody(): Body
    {
        return $this->body;
    }

    public function getCcRecipients(): RecipientCollection
    {
        return $this->ccRecipients;
    }

    public function getBccRecipients(): RecipientCollection
    {
        return $this->bccRecipients;
    }
}

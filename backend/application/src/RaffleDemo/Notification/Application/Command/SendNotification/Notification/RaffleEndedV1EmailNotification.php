<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Command\SendNotification\Notification;

use App\Foundation\DomainEventRegistry\Raffle\RaffleEndedV1Event;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;
use DateTimeInterface;

/** @infection-ignore-all */
final readonly class RaffleEndedV1EmailNotification implements NotificationInterface
{
    private const string TYPE = 'notification.email.raffle.ended.v1';

    private function __construct(
        private NotificationId $id,
        private EmailAddress $recipient,
        private EmailAddress $sender,
        private string $name,
        private DateTimeInterface $endedAt,
        private string $endedReason,
    ) {
    }

    public static function fromEvent(RaffleEndedV1Event $event): self
    {
        return new self(
            id: NotificationId::fromString($event->getEventId()),
            recipient: EmailAddress::fromString($event->createdBy),
            sender: EmailAddress::fromString('do-not-reply@example.com'),
            name: $event->name,
            endedAt: $event->endedAt,
            endedReason: $event->endedReason,
        );
    }

    public function getId(): NotificationId
    {
        return $this->id;
    }

    public function getType(): Type
    {
        return Type::fromString(self::TYPE);
    }

    public function getChannel(): Channel
    {
        return Channel::EMAIL;
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
        $subject = <<<'TEXT'
        Your raffle has been ended!
        TEXT;

        return Subject::fromString($subject);
    }

    public function getBody(): Body
    {
        $body = <<<TEXT
        Hey!

        Your raffle {$this->name} has been ended at {$this->endedAt->format('Y-m-d H:i:s')}.

        This is due to: {$this->endedReason}.

        Thanks,
        RaffleDemo.
        TEXT;

        return Body::fromString($body);
    }

    public function getCcRecipients(): RecipientCollection
    {
        return RecipientCollection::fromNew();
    }

    public function getBccRecipients(): RecipientCollection
    {
        return RecipientCollection::fromNew();
    }
}

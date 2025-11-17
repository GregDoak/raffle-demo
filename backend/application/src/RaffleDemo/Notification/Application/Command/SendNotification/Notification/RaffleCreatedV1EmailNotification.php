<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Command\SendNotification\Notification;

use App\Foundation\DomainEventRegistry\Raffle\RaffleCreatedV1Event;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;
use DateTimeInterface;

/** @infection-ignore-all */
final readonly class RaffleCreatedV1EmailNotification implements NotificationInterface
{
    private const string TYPE = 'notification.email.raffle.created.v1';

    private function __construct(
        private NotificationId $id,
        private EmailAddress $recipient,
        private EmailAddress $sender,
        private string $name,
        private string $prize,
        private DateTimeInterface $startAt,
        private int $totalTickets,
    ) {
    }

    public static function fromEvent(RaffleCreatedV1Event $event): self
    {
        return new self(
            NotificationId::fromString($event->getEventId()),
            EmailAddress::fromString($event->createdBy),
            EmailAddress::fromString('do-not-reply@example.com'),
            $event->name,
            $event->prize,
            $event->startAt,
            $event->totalTickets,
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
        Your raffle has been created!
        TEXT;

        return Subject::fromString($subject);
    }

    public function getBody(): Body
    {
        $body = <<<TEXT
        Hey!

        Your raffle {$this->name} has been created and is due to start at {$this->startAt->format('Y-m-d H:i:s')}.

        Out of {$this->totalTickets} available tickets a lucky winner will receive a {$this->prize}.

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

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Command\SendNotification\Notification;

use App\Foundation\DomainEventRegistry\Raffle\TicketAllocatedToParticipantV1Event;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;
use DateTimeInterface;

final readonly class TicketAllocatedToParticipantV1EventParticipantEmailNotification implements NotificationInterface
{
    private const string TYPE = 'notification.email.raffle.ticket_allocated_to_participant.participant.v1';

    private function __construct(
        private NotificationId $id,
        private EmailAddress $recipient,
        private EmailAddress $sender,
        private string $name,
        private DateTimeInterface $allocatedAt,
        private int $allocatedQuantity,
        private DateTimeInterface $drawAt,
    ) {
    }

    public static function fromEvent(TicketAllocatedToParticipantV1Event $event): self
    {
        return new self(
            id: NotificationId::fromNew(),
            recipient: EmailAddress::fromString($event->allocatedTo),
            sender: EmailAddress::fromString('do-not-reply@example.com'),
            name: $event->name,
            allocatedAt: $event->allocatedAt,
            allocatedQuantity: $event->allocatedQuantity,
            drawAt: $event->drawAt,
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
        Your ticket allocation confirmation!
        TEXT;

        return Subject::fromString($subject);
    }

    public function getBody(): Body
    {
        $body = <<<TEXT
        Hey!

        We're received your allocation to the {$this->name} raffle.

        Allocated at: {$this->allocatedAt->format('Y-m-d H:i:s')}
        Quantity: {$this->allocatedQuantity}

        Good luck, this raffle is due to be drawn at {$this->drawAt->format('Y-m-d H:i:s')}

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

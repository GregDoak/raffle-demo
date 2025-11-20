<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Command\SendNotification\Notification;

use App\Foundation\DomainEventRegistry\Raffle\RaffleDrawnV1Event;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;
use DateTimeInterface;

/** @infection-ignore-all */
final readonly class RaffleDrawnV1CreatorEmailNotification implements NotificationInterface
{
    private const string TYPE = 'notification.email.raffle.drawn.creator.v1';

    private function __construct(
        private NotificationId $id,
        private EmailAddress $recipient,
        private EmailAddress $sender,
        private string $name,
        private string $prize,
        private DateTimeInterface $drawnAt,
        private int $totalTickets,
        private int $winningTicketNumber,
        private string $winningRecipient,
    ) {
    }

    public static function fromEvent(RaffleDrawnV1Event $event): self
    {
        return new self(
            id: NotificationId::fromNew(),
            recipient: EmailAddress::fromString($event->createdBy),
            sender: EmailAddress::fromString('do-not-reply@example.com'),
            name: $event->name,
            prize: $event->prize,
            drawnAt: $event->drawnAt,
            totalTickets: $event->totalTickets,
            winningTicketNumber: $event->winningTicketNumber,
            winningRecipient: $event->winningAllocationTo,
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
        Your raffle has been drawn!
        TEXT;

        return Subject::fromString($subject);
    }

    public function getBody(): Body
    {
        $body = <<<TEXT
        Hey!

        Your raffle {$this->name} has been drawn at {$this->drawnAt->format('Y-m-d H:i:s')}.

        This winning ticket number is {$this->winningTicketNumber} from {$this->totalTickets} available tickets.

        Your raffle was won by {$this->winningRecipient} who will receive a {$this->prize}.

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

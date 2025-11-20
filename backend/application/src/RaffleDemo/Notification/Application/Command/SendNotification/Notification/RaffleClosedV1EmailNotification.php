<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Application\Command\SendNotification\Notification;

use App\Foundation\DomainEventRegistry\Raffle\RaffleClosedV1Event;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;
use DateTimeInterface;

/** @infection-ignore-all */
final readonly class RaffleClosedV1EmailNotification implements NotificationInterface
{
    private const string TYPE = 'notification.email.raffle.closed.v1';

    private function __construct(
        private NotificationId $id,
        private EmailAddress $recipient,
        private EmailAddress $sender,
        private string $name,
        private string $prize,
        private DateTimeInterface $closedAt,
        private DateTimeInterface $drawAt,
        private int $totalTickets,
        private int $numberOfTicketsAllocated,
    ) {
    }

    public static function fromEvent(RaffleClosedV1Event $event): self
    {
        return new self(
            id: NotificationId::fromString($event->getEventId()),
            recipient: EmailAddress::fromString($event->createdBy),
            sender: EmailAddress::fromString('do-not-reply@example.com'),
            name: $event->name,
            prize: $event->prize,
            closedAt: $event->closedAt,
            drawAt: $event->drawAt,
            totalTickets: $event->totalTickets,
            numberOfTicketsAllocated: $event->numberOfTicketsAllocated,
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
        Your raffle has been closed!
        TEXT;

        return Subject::fromString($subject);
    }

    public function getBody(): Body
    {
        $noAllocationsBody = <<<TEXT
        Hey!

        Your raffle {$this->name} has been closed at {$this->closedAt->format('Y-m-d H:i:s')}.

        As there are no allocations, this raffle will not be drawn.

        Thanks,
        RaffleDemo.
        TEXT;

        $withAllocationsBody = <<<TEXT
        Hey!

        Your raffle {$this->name} has been closed at {$this->closedAt->format('Y-m-d H:i:s')}.

        You have had {$this->numberOfTicketsAllocated} tickets allocated out of a possible {$this->totalTickets} available tickets.

        Your raffle will be drawn at {$this->drawAt->format('Y-m-d H:i:s')} where a lucky winner will receive a {$this->prize}.

        Thanks,
        RaffleDemo.
        TEXT;

        return Body::fromString(($this->numberOfTicketsAllocated === 0) ? $noAllocationsBody : $withAllocationsBody);
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

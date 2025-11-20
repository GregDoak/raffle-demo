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

final readonly class TicketAllocatedToParticipantV1EventCreatorEmailNotification implements NotificationInterface
{
    private const string TYPE = 'notification.email.raffle.ticket_allocated_to_participant.creator.v1';

    private function __construct(
        private NotificationId $id,
        private EmailAddress $recipient,
        private EmailAddress $sender,
        private string $name,
        private DateTimeInterface $allocatedAt,
        private int $allocatedQuantity,
        private string $allocatedTo,
        private int $totalTickets,
        private int $numberOfTicketsAllocated,
    ) {
    }

    public static function fromEvent(TicketAllocatedToParticipantV1Event $event): self
    {
        return new self(
            id: NotificationId::fromNew(),
            recipient: EmailAddress::fromString($event->createdBy),
            sender: EmailAddress::fromString('do-not-reply@example.com'),
            name: $event->name,
            allocatedAt: $event->allocatedAt,
            allocatedQuantity: $event->allocatedQuantity,
            allocatedTo: $event->allocatedTo,
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
        An allocation has been added to your raffle!
        TEXT;

        return Subject::fromString($subject);
    }

    public function getBody(): Body
    {
        $body = <<<TEXT
        Hey!

        Your raffle {$this->name} has had a new allocation.

        Allocated at: {$this->allocatedAt->format('Y-m-d H:i:s')}
        Allocated to: {$this->allocatedTo}
        Quantity: {$this->allocatedQuantity}

        The raffle has allocated {$this->numberOfTicketsAllocated} tickets out of {$this->totalTickets}.

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

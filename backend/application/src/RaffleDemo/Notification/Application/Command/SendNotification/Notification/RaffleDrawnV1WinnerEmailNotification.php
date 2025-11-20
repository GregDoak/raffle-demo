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

/** @infection-ignore-all */
final readonly class RaffleDrawnV1WinnerEmailNotification implements NotificationInterface
{
    private const string TYPE = 'notification.email.raffle.drawn.winner.v1';

    private function __construct(
        private NotificationId $id,
        private EmailAddress $recipient,
        private EmailAddress $sender,
        private string $name,
        private string $prize,
        private int $winningTicketNumber,
    ) {
    }

    public static function fromEvent(RaffleDrawnV1Event $event): self
    {
        return new self(
            id: NotificationId::fromNew(),
            recipient: EmailAddress::fromString($event->winningAllocationTo),
            sender: EmailAddress::fromString('do-not-reply@example.com'),
            name: $event->name,
            prize: $event->prize,
            winningTicketNumber: $event->winningTicketNumber,
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
        You've won!
        TEXT;

        return Subject::fromString($subject);
    }

    public function getBody(): Body
    {
        $body = <<<TEXT
        Hey!

        Congratulations! You won the Raffle {$this->name}!

        You have won a {$this->prize} with your ticket number {$this->winningTicketNumber}.

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

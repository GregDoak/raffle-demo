<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Infrastructure\Sender;

use App\RaffleDemo\Notification\Application\Command\SendNotification\Notification\NotificationInterface;
use App\RaffleDemo\Notification\Application\Service\Sender\SenderInterface;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final readonly class SymfonyMailerSender implements SenderInterface
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function supports(NotificationInterface $notification): bool
    {
        return $notification->getChannel() === Channel::EMAIL; // @phpstan-ignore-line identical.alwaysTrue
    }

    public function send(NotificationInterface $notification): void
    {
        $email = new Email()
            ->from($notification->getSender()->toString())
            ->to($notification->getRecipient()->toString())
            ->cc(...$notification->getCcRecipients()->toArray())
            ->bcc(...$notification->getBccRecipients()->toArray())
            ->subject($notification->getSubject()->toString())
            ->text($notification->getBody()->toString())
            ->html($notification->getBody()->toString());

        $this->mailer->send($email);
    }
}

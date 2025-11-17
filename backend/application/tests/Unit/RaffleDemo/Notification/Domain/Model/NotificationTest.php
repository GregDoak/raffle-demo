<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Notification\Domain\Model;

use App\RaffleDemo\Notification\Domain\Model\Notification;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\Body;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Domain\ValueObject\RecipientCollection;
use App\RaffleDemo\Notification\Domain\ValueObject\Status;
use App\RaffleDemo\Notification\Domain\ValueObject\Subject;
use App\RaffleDemo\Notification\Domain\ValueObject\Type;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NotificationTest extends TestCase
{
    #[Test]
    public function it_creates_a_notification(): void
    {
        // Arrange
        $id = NotificationId::fromNew();
        $type = Type::fromString('notification.dummy.v1');
        $channel = Channel::EMAIL;
        $recipient = EmailAddress::fromString('recipient@example.com');
        $ccRecipients = RecipientCollection::fromArray(
            EmailAddress::fromString('cc.recipient.1@example.com'),
            EmailAddress::fromString('cc.recipient.2@example.com'),
        );
        $bccRecipients = RecipientCollection::fromArray(
            EmailAddress::fromString('bcc.recipient.1@example.com'),
            EmailAddress::fromString('bcc.recipient.2@example.com'),
        );
        $sender = EmailAddress::fromString('sender@example.com');
        $subject = Subject::fromString('subject');
        $body = Body::fromString('body');
        $status = Status::SENT;

        // Act
        $notification = Notification::create(
            $id,
            $type,
            $channel,
            $recipient,
            $ccRecipients,
            $bccRecipients,
            $sender,
            $subject,
            $body,
            $status,
        );

        // Assert
        self::assertSame($id, $notification->id);
        self::assertSame($type, $notification->type);
        self::assertSame($channel, $notification->channel);
        self::assertSame($recipient, $notification->recipient);
        self::assertSame($ccRecipients, $notification->ccRecipients);
        self::assertSame($bccRecipients, $notification->bccRecipients);
        self::assertSame($sender, $notification->sender);
        self::assertSame($subject, $notification->subject);
        self::assertSame($body, $notification->body);
        self::assertSame($status, $notification->status);
    }
}

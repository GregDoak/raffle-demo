<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Notification\Application\Service\Notifier;

use App\RaffleDemo\Notification\Application\Service\Notifier\Notifier;
use App\RaffleDemo\Notification\Application\Service\Notifier\UnsupportedNotificationException;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\Tests\Context\RaffleDemo\Notification\Application\Command\SendNotification\Notification\StubNotification;
use App\Tests\Double\RaffleDemo\Notification\Infrastructure\Sender\EmailSenderSpy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NotifierTest extends TestCase
{
    #[Test]
    public function it_successfully_sends_a_notification(): void
    {
        // Arrange
        $sender = new EmailSenderSpy();
        $notifier = new Notifier([$sender]);
        $notification = StubNotification::create(
            channel: Channel::EMAIL,
        );

        // Act
        $notifier->notify($notification);

        // Assert
        self::assertCount(1, $sender->notifications);
        self::assertSame($notification, $sender->notifications[$notification->getId()->toString()]);
    }

    #[Test]
    public function it_fails_to_send_a_notification_when_no_valid_sender_exists(): void
    {
        // Arrange
        $notifier = new Notifier([]);
        $notification = StubNotification::create(
            channel: Channel::EMAIL,
        );

        // Act
        self::expectException(UnsupportedNotificationException::class);
        $notifier->notify($notification);

        // Assert
        self::fail();
    }
}

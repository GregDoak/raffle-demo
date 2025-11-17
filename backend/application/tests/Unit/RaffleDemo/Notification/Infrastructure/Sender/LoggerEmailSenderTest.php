<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Notification\Infrastructure\Sender;

use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Infrastructure\Sender\LoggerEmailSender;
use App\Tests\Context\RaffleDemo\Notification\Application\Command\SendNotification\Notification\StubNotification;
use App\Tests\Double\Framework\Infrastructure\Logger\InMemoryLogger;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoggerEmailSenderTest extends TestCase
{
    private InMemoryLogger $logger;
    private LoggerEmailSender $sender;

    protected function setUp(): void
    {
        $this->logger = new InMemoryLogger();

        $this->sender = new LoggerEmailSender($this->logger);
    }

    #[Test]
    public function it_supports_email_channel(): void
    {
        // Arrange
        $notification = StubNotification::create(
            channel: Channel::EMAIL,
        );

        // Act
        $result = $this->sender->supports($notification);

        // Assert
        self::assertTrue($result);
    }

    #[Test]
    public function it_sends_a_notification(): void
    {
        // Arrange
        $notification = StubNotification::create(
            channel: Channel::EMAIL,
        );

        // Act
        $this->sender->send($notification);

        // Assert
        $logs = $this->logger->getLogsForLevel('notice');
        self::assertCount(1, $logs);
        self::assertStringContainsString('Sending notification', $logs[0]['message']);
    }
}

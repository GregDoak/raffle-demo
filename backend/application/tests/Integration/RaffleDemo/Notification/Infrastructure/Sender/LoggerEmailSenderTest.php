<?php

declare(strict_types=1);

namespace App\Tests\Integration\RaffleDemo\Notification\Infrastructure\Sender;

use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Infrastructure\Sender\LoggerEmailSender;
use App\Tests\Context\RaffleDemo\Notification\Application\Command\SendNotification\Notification\StubNotification;
use App\Tests\Integration\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\Test;

final class LoggerEmailSenderTest extends AbstractIntegrationTestCase
{
    private LoggerEmailSender $sender;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sender = self::getContainer()->get(LoggerEmailSender::class);
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
        self::expectNotToPerformAssertions();
    }
}

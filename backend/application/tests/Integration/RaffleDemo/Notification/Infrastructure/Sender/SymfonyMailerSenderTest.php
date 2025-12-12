<?php

declare(strict_types=1);

namespace App\Tests\Integration\RaffleDemo\Notification\Infrastructure\Sender;

use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\RaffleDemo\Notification\Infrastructure\Sender\SymfonyMailerSender;
use App\Tests\Context\RaffleDemo\Notification\Application\Command\SendNotification\Notification\StubNotification;
use App\Tests\Integration\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\Test;

final class SymfonyMailerSenderTest extends AbstractIntegrationTestCase
{
    private SymfonyMailerSender $sender;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sender = self::getContainer()->get(SymfonyMailerSender::class);
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
        self::assertEmailCount(1);
    }
}

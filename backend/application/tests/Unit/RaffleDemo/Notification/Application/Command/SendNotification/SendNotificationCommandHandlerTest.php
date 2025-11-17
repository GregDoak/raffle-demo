<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Notification\Application\Command\SendNotification;

use App\RaffleDemo\Notification\Application\Command\SendNotification\SendNotificationCommand;
use App\RaffleDemo\Notification\Application\Command\SendNotification\SendNotificationCommandHandler;
use App\RaffleDemo\Notification\Application\Service\Notifier\Notifier;
use App\RaffleDemo\Notification\Domain\Exception\InvalidNotificationException;
use App\RaffleDemo\Notification\Domain\ValueObject\Channel;
use App\Tests\Context\RaffleDemo\Notification\Application\Command\SendNotification\Notification\StubNotification;
use App\Tests\Double\Framework\Domain\Repository\TransactionBoundarySpy;
use App\Tests\Double\RaffleDemo\Notification\Infrastructure\Repository\InMemoryNotificationRepository;
use App\Tests\Double\RaffleDemo\Notification\Infrastructure\Sender\AbstractSenderSpy;
use App\Tests\Double\RaffleDemo\Notification\Infrastructure\Sender\EmailSenderSpy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

final class SendNotificationCommandHandlerTest extends TestCase
{
    private SendNotificationCommandHandler $handler;
    private Notifier $notifier;
    private InMemoryNotificationRepository $repository;
    private AbstractSenderSpy $sender;
    private TransactionBoundarySpy $transactionBoundary;

    protected function setUp(): void
    {
        $this->repository = new InMemoryNotificationRepository();

        $this->sender = new EmailSenderSpy();
        $this->notifier = new Notifier([$this->sender]);
        $this->transactionBoundary = new TransactionBoundarySpy();

        $this->handler = new SendNotificationCommandHandler(
            repository: $this->repository,
            notifier: $this->notifier,
            transactionBoundary: $this->transactionBoundary,
        );
    }

    #[Test]
    public function it_success_stores_and_notifies_a_new_notification(): void
    {
        // Arrange
        $expectedNotification = StubNotification::create(
            channel: Channel::EMAIL,
        );

        // Act
        $this->handler->__invoke(SendNotificationCommand::create($expectedNotification));

        // Assert
        self::assertTrue($this->transactionBoundary->hasBegun);
        self::assertTrue($this->transactionBoundary->hasCommitted);
        self::assertFalse($this->transactionBoundary->hasRolledBack);

        $notifications = $this->repository->notifications;
        self::assertCount(1, $notifications);
        self::assertArrayHasKey($expectedNotification->getId()->toString(), $notifications);
        $notification = $notifications[$expectedNotification->getId()->toString()];

        self::assertSame($expectedNotification->getId()->toString(), $notification->id->toString());
        self::assertSame($expectedNotification->getChannel(), $notification->channel);

        self::assertCount(1, $this->sender->notifications);
    }

    #[Test]
    public function it_fails_when_processing_a_duplicate_notification(): void
    {
        // Arrange
        $expectedNotification = StubNotification::create(
            channel: Channel::EMAIL,
        );
        $this->handler->__invoke(SendNotificationCommand::create($expectedNotification));
        $this->transactionBoundary->reset();
        $exception = null;
        $expectedException = InvalidNotificationException::fromDuplicateNotification();

        // Act
        try {
            $this->handler->__invoke(SendNotificationCommand::create($expectedNotification));
        } catch (Throwable $exception) {
        }

        // Assert
        self::assertTrue($this->transactionBoundary->hasBegun);
        self::assertFalse($this->transactionBoundary->hasCommitted);
        self::assertTrue($this->transactionBoundary->hasRolledBack);
        self::assertEquals($expectedException, $exception);

        self::assertCount(1, $this->repository->notifications);
        self::assertCount(1, $this->sender->notifications);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Integration\RaffleDemo\Notification\Infrastructure\Repository;

use App\RaffleDemo\Notification\Domain\Exception\InvalidNotificationException;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\ValueObject\EmailAddress;
use App\RaffleDemo\Notification\Infrastructure\Repository\PostgresNotificationRepository;
use App\Tests\Context\RaffleDemo\Notification\Domain\Model\NotificationDomainContext;
use App\Tests\Integration\AbstractIntegrationTestCase;
use PHPUnit\Framework\Attributes\Test;

final class PostgresNotificationRepositoryTest extends AbstractIntegrationTestCase
{
    private PostgresNotificationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(PostgresNotificationRepository::class);
    }

    #[Test]
    public function it_stores_a_notification(): void
    {
        // Arrange
        $expectedNotification = NotificationDomainContext::create();

        // Act
        $this->repository->store($expectedNotification);

        // Assert
        $notifications = $this->repository->getByRecipient($expectedNotification->recipient);
        self::assertCount(1, $notifications);
        self::assertEquals($expectedNotification, $notifications[0]);
    }

    #[Test]
    public function it_retrieves_a_notifications_for_a_given_id(): void
    {
        // Arrange
        $expectedNotification = NotificationDomainContext::create();
        $this->repository->store($expectedNotification);

        // Act
        $notification = $this->repository->getById($expectedNotification->id);

        // Assert
        self::assertEquals($expectedNotification, $notification);
    }

    #[Test]
    public function it_does_not_retrieve_a_notification_for_a_non_existing_id(): void
    {
        // Arrange
        $id = NotificationId::fromNew();

        // Act
        $notification = $this->repository->getById($id);

        // Assert
        self::assertNull($notification);
    }

    #[Test]
    public function it_retrieves_notifications_for_a_given_recipient_sorted_in_descending_order(): void
    {
        // Arrange
        $expectedRecipient = EmailAddress::fromString('recipient@example.com');
        $expectedNotifications = [
            NotificationDomainContext::create(recipient: $expectedRecipient),
            NotificationDomainContext::create(recipient: $expectedRecipient),
            NotificationDomainContext::create(recipient: $expectedRecipient),
        ];
        $unexpectedNotifications = [
            NotificationDomainContext::create(
                recipient: EmailAddress::fromString('another.recipient@example.com'),
            ),
        ];

        foreach (array_merge($expectedNotifications, $unexpectedNotifications) as $notification) {
            $this->repository->store($notification);
        }

        // Act
        $notifications = $this->repository->getByRecipient($expectedNotifications[0]->recipient);

        // Assert
        self::assertCount(3, $notifications);
        self::assertEquals($expectedNotifications[0], $notifications[2]);
        self::assertEquals($expectedNotifications[1], $notifications[1]);
        self::assertEquals($expectedNotifications[2], $notifications[0]);
    }

    #[Test]
    public function it_cannot_store_a_duplicate_notification(): void
    {
        // Arrange
        $notification = NotificationDomainContext::create();
        $this->repository->store($notification);

        // Act
        self::expectException(InvalidNotificationException::class);
        $this->repository->store($notification);

        // Assert
        self::fail();
    }
}

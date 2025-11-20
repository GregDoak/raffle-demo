<?php

declare(strict_types=1);

namespace App\Tests\Functional\RaffleDemo\Notification\UserInterface\Event\RaffleClosedV1;

use App\Foundation\Clock\Clock;
use App\Foundation\DomainEventRegistry\Raffle\RaffleClosedV1Event;
use App\RaffleDemo\Notification\Domain\Model\Notification;
use App\RaffleDemo\Notification\Domain\Model\NotificationId;
use App\RaffleDemo\Notification\Domain\Repository\NotificationRepositoryInterface;
use App\RaffleDemo\Notification\UserInterface\Event\RaffleClosedV1\RaffleClosedV1EventSubscriber;
use App\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;

final class RaffleClosedV1EventSubscriberTest extends AbstractFunctionalTestCase
{
    private NotificationRepositoryInterface $repository;
    private RaffleClosedV1EventSubscriber $subscriber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(NotificationRepositoryInterface::class);
        $this->subscriber = self::getContainer()->get(RaffleClosedV1EventSubscriber::class);
    }

    #[Test]
    public function it_handles_an_incoming_event(): void
    {
        // Arrange
        $event = RaffleClosedV1Event::fromPayload(
            eventId: 'e7d1ed63-33c7-4cfa-a420-b2aab66c5293',
            eventOccurredAt: Clock::fromString('2025-01-01 00:00:00'),
            payload: [
                'id' => '0f87dda7-8a97-485d-9fbb-ff99cd80c1e0',
                'name' => 'raffle-name',
                'prize' => 'raffle-prize',
                'createdAt' => '2025-01-01 00:00:00',
                'createdBy' => 'recipient@example.com',
                'startAt' => '2025-01-02 00:00:00',
                'closedAt' => '2025-01-03 00:00:01',
                'closedBy' => 'system',
                'closeAt' => '2025-01-03 00:00:00',
                'drawAt' => '2025-01-04 00:00:00',
                'totalTickets' => 10,
                'numberOfTicketsAllocated' => 5,
                'ticketAmount' => 1,
                'ticketCurrency' => 'ticket-currency',
            ],
        );

        // Act
        $this->subscriber->__invoke($event);

        // Assert
        $notification = $this->repository->getById(NotificationId::fromString($event->getEventId()));
        self::assertInstanceOf(Notification::class, $notification);
    }
}

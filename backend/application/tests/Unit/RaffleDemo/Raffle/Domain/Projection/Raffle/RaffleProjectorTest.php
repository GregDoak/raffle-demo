<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Projection\Raffle;

use App\RaffleDemo\Raffle\Domain\Projection\Raffle\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjector;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Model\RaffleDomainContext;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\InMemoryRaffleProjectionRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleProjectorTest extends TestCase
{
    private InMemoryRaffleProjectionRepository $repository;
    private RaffleProjector $projector;

    protected function setUp(): void
    {
        $this->repository = new InMemoryRaffleProjectionRepository();
        $this->projector = new RaffleProjector($this->repository);
    }

    #[Test]
    public function it_projects_the_raffle_created_event(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(1, $this->repository->raffles);
        self::assertInstanceOf(Raffle::class, $this->repository->getById($raffle->getAggregateId()->toString()));
    }

    #[Test]
    public function it_projects_the_raffle_started_event(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(1, $this->repository->raffles);
        self::assertInstanceOf(Raffle::class, $this->repository->getById($raffle->getAggregateId()->toString()));
        self::assertSame(
            $raffle->started?->at,
            $this->repository->getById($raffle->getAggregateId()->toString())->startedAt,
        );
    }

    #[Test]
    public function it_does_not_project_the_raffle_started_event_when_a_raffle_is_not_created(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle->flushEvents();
        $raffle = RaffleDomainContext::start($raffle);
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(0, $this->repository->raffles);
        self::assertNull($this->repository->getById($raffle->getAggregateId()->toString()));
    }

    #[Test]
    public function it_projects_the_ticket_allocated_to_participant_event(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle);
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(1, $this->repository->raffles);
        self::assertInstanceOf(Raffle::class, $this->repository->getById($raffle->getAggregateId()->toString()));
        self::assertLessThan(
            $raffle->totalTickets->toInt(),
            $this->repository->getById($raffle->getAggregateId()->toString())->remainingTickets,
        );
    }

    #[Test]
    public function it_does_not_project_the_ticket_allocated_to_participant_event_when_a_raffle_is_not_created(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle->flushEvents();
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle);
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(0, $this->repository->raffles);
        self::assertNull($this->repository->getById($raffle->getAggregateId()->toString()));
    }

    #[Test]
    public function it_projects_the_raffle_closed_event(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle);
        $raffle = RaffleDomainContext::close($raffle);
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(1, $this->repository->raffles);
        self::assertInstanceOf(Raffle::class, $this->repository->getById($raffle->getAggregateId()->toString()));
        self::assertSame(
            $raffle->closed?->at,
            $this->repository->getById($raffle->getAggregateId()->toString())->closedAt,
        );
    }

    #[Test]
    public function it_does_not_project_the_raffle_closed_event_when_a_raffle_is_not_created(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle);
        $raffle->flushEvents();
        $raffle = RaffleDomainContext::close($raffle);
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(0, $this->repository->raffles);
        self::assertNull($this->repository->getById($raffle->getAggregateId()->toString()));
    }

    #[Test]
    public function it_projects_the_prize_drawn_event(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle);
        $raffle = RaffleDomainContext::close($raffle);
        $raffle = RaffleDomainContext::drawPrize($raffle);
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(1, $this->repository->raffles);
        self::assertInstanceOf(Raffle::class, $this->repository->getById($raffle->getAggregateId()->toString()));
        self::assertSame(
            $raffle->drawn?->at,
            $this->repository->getById($raffle->getAggregateId()->toString())->drawnAt,
        );
    }

    #[Test]
    public function it_does_not_project_prize_drawn_event_when_a_raffle_is_not_created(): void
    {
        // Arrange
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle);
        $raffle = RaffleDomainContext::close($raffle);
        $raffle->flushEvents();
        $raffle = RaffleDomainContext::drawPrize($raffle);
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(0, $this->repository->raffles);
        self::assertNull($this->repository->getById($raffle->getAggregateId()->toString()));
    }
}

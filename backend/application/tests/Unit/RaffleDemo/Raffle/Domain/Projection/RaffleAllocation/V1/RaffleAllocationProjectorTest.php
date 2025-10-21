<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocation;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationProjector;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationQuery;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Model\RaffleDomainContext;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\RaffleAllocation\V1\InMemoryRaffleAllocationProjectionRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleAllocationProjectorTest extends TestCase
{
    private InMemoryRaffleAllocationProjectionRepository $repository;
    private RaffleAllocationProjector $projector;

    protected function setUp(): void
    {
        $this->repository = new InMemoryRaffleAllocationProjectionRepository();
        $this->projector = new RaffleAllocationProjector($this->repository);
    }

    #[Test]
    public function it_projects_the_ticket_allocated_to_participant_event(): void
    {
        // Arrange
        $expectedTicketAllocation = TicketAllocation::from(
            quantity: 1,
            allocatedTo: 'participant',
            allocatedAt: Clock::fromString('2025-01-02 00:00:01'),
        );
        $raffle = RaffleDomainContext::create();
        $raffle = RaffleDomainContext::start($raffle);
        $raffle = RaffleDomainContext::allocateTicketToParticipant($raffle, $expectedTicketAllocation);
        $events = $raffle->flushEvents();

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(1, $this->repository->raffleAllocations);
        $raffleAllocation = $this->repository->query(new RaffleAllocationQuery()->withRaffleId($raffle->getAggregateId()->toString()))[0];
        self::assertInstanceOf(RaffleAllocation::class, $raffleAllocation);
        self::assertSame($raffle->getAggregateId()->toString(), $raffleAllocation->raffleId);
        self::assertSame($expectedTicketAllocation->allocatedAt, $raffleAllocation->allocatedAt);
        self::assertSame($expectedTicketAllocation->allocatedTo, $raffleAllocation->allocatedTo);
        self::assertSame($expectedTicketAllocation->quantity, $raffleAllocation->quantity);
    }
}

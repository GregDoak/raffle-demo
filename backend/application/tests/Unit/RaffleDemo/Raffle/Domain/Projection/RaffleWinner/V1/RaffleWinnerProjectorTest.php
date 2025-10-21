<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1;

use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinner;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinnerProjector;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinnerQuery;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Model\RaffleDomainContext;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\RaffleWinner\V1\InMemoryRaffleWinnerProjectionRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleWinnerProjectorTest extends TestCase
{
    private InMemoryRaffleWinnerProjectionRepository $repository;
    private RaffleWinnerProjector $projector;

    protected function setUp(): void
    {
        $this->repository = new InMemoryRaffleWinnerProjectionRepository();
        $this->projector = new RaffleWinnerProjector($this->repository);
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
        $expectedDrawn = $raffle->drawn;
        $expectedWinner = $raffle->winner;

        // Act
        $this->projector->__invoke($events);

        // Assert
        self::assertCount(1, $this->repository->raffleWinners);
        $raffleWinner = $this->repository->query(new RaffleWinnerQuery()->withRaffleId($raffle->getAggregateId()->toString()))[0];
        self::assertInstanceOf(RaffleWinner::class, $raffleWinner);
        self::assertNotNull($expectedDrawn);
        self::assertNotNull($expectedWinner);
        self::assertSame($raffle->getAggregateId()->toString(), $raffleWinner->raffleId);
        self::assertSame($expectedWinner->ticketAllocation->hash, $raffleWinner->raffleAllocationHash);
        self::assertSame($expectedDrawn->at, $raffleWinner->drawnAt);
        self::assertSame($expectedWinner->ticketNumber, $raffleWinner->winningTicketNumber);
        self::assertSame($expectedWinner->ticketAllocation->allocatedTo, $raffleWinner->winner);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Projection\Raffle\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleQueryFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleQueryFactoryTest extends TestCase
{
    #[Test]
    public function it_can_create_a_raffles_due_to_be_started_query(): void
    {
        // Arrange
        $now = Clock::now();
        $limit = 10;
        $offset = 5;

        // Act
        $query = RaffleQueryFactory::getRafflesDueToBeStartedQuery($now);
        $queryWithLimit = RaffleQueryFactory::getRafflesDueToBeStartedQuery($now, $limit);
        $queryWithLimitAndOffset = RaffleQueryFactory::getRafflesDueToBeStartedQuery($now, $limit, $offset);

        // Assert
        self::assertNull($query->name);
        self::assertNull($query->prize);
        self::assertSame('created', $query->status);
        self::assertSame($now, $query->startAt);
        self::assertNull($query->closeAt);
        self::assertNull($query->drawAt);
        self::assertNull($query->limit);
        self::assertSame(0, $query->offset);
        self::assertSame('startAt', $query->sortField);
        self::assertSame('ASC', $query->sortOrder);

        self::assertSame($limit, $queryWithLimit->limit);
        self::assertSame(0, $queryWithLimit->offset);

        self::assertSame($limit, $queryWithLimitAndOffset->limit);
        self::assertSame($offset, $queryWithLimitAndOffset->offset);
    }

    #[Test]
    public function it_can_create_a_raffles_due_to_be_closed_query(): void
    {
        // Arrange
        $now = Clock::now();
        $limit = 10;
        $offset = 5;

        // Act
        $query = RaffleQueryFactory::getRafflesDueToBeClosedQuery($now);
        $queryWithLimit = RaffleQueryFactory::getRafflesDueToBeClosedQuery($now, $limit);
        $queryWithLimitAndOffset = RaffleQueryFactory::getRafflesDueToBeClosedQuery($now, $limit, $offset);

        // Assert
        self::assertNull($query->name);
        self::assertNull($query->prize);
        self::assertSame('started', $query->status);
        self::assertNull($query->startAt);
        self::assertSame($now, $query->closeAt);
        self::assertNull($query->drawAt);
        self::assertNull($query->limit);
        self::assertSame(0, $query->offset);
        self::assertSame('closeAt', $query->sortField);
        self::assertSame('ASC', $query->sortOrder);

        self::assertSame($limit, $queryWithLimit->limit);
        self::assertSame(0, $queryWithLimit->offset);

        self::assertSame($limit, $queryWithLimitAndOffset->limit);
        self::assertSame($offset, $queryWithLimitAndOffset->offset);
    }

    #[Test]
    public function it_can_create_a_raffles_due_to_be_drawn_query(): void
    {
        // Arrange
        $now = Clock::now();
        $limit = 10;
        $offset = 5;

        // Act
        $query = RaffleQueryFactory::getRafflesDueToBeDrawnQuery($now);
        $queryWithLimit = RaffleQueryFactory::getRafflesDueToBeDrawnQuery($now, $limit);
        $queryWithLimitAndOffset = RaffleQueryFactory::getRafflesDueToBeDrawnQuery($now, $limit, $offset);

        // Assert
        self::assertNull($query->name);
        self::assertNull($query->prize);
        self::assertSame('closed', $query->status);
        self::assertNull($query->startAt);
        self::assertNull($query->closeAt);
        self::assertSame($now, $query->drawAt);
        self::assertNull($query->limit);
        self::assertSame(0, $query->offset);
        self::assertSame('drawAt', $query->sortField);
        self::assertSame('ASC', $query->sortOrder);

        self::assertSame($limit, $queryWithLimit->limit);
        self::assertSame(0, $queryWithLimit->offset);

        self::assertSame($limit, $queryWithLimitAndOffset->limit);
        self::assertSame($offset, $queryWithLimitAndOffset->offset);
    }
}

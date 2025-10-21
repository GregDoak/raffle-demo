<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinnerQuery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleWinnerQueryTest extends TestCase
{
    #[Test]
    public function it_can_create_a_query(): void
    {
        // Arrange
        $raffleId = 'raffle-id';
        $drawnAt = Clock::fromString('2025-01-01 00:00:00');
        $limit = 10;
        $offset = 5;
        $sortField = 'raffleId';
        $sortOrder = 'DESC';

        // Act
        $query = new RaffleWinnerQuery()
            ->withRaffleId($raffleId)
            ->withDrawnAt($drawnAt)
            ->paginate($limit, $offset)
            ->sortBy($sortField, $sortOrder);

        // Assert
        self::assertSame($raffleId, $query->raffleId);
        self::assertSame($drawnAt, $query->drawnAt);
        self::assertSame($limit, $query->limit);
        self::assertSame($offset, $query->offset);
        self::assertSame($sortField, $query->sortField);
        self::assertSame($sortOrder, $query->sortOrder);
    }

    #[Test]
    public function it_cannot_set_the_sort_field_by_a_non_existing_field(): void
    {
        // Arrange
        $query = new RaffleWinnerQuery();

        // Act
        $query = $query->sortBy('INVALID');

        // Assert
        self::assertNull($query->sortField);
    }
}

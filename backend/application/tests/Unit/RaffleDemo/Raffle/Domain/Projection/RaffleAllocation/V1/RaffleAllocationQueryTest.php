<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1;

use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationQuery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleAllocationQueryTest extends TestCase
{
    #[Test]
    public function it_can_create_a_query(): void
    {
        // Arrange
        $raffleId = 'raffle-id';
        $limit = 10;
        $offset = 5;
        $sortField = 'raffleId';
        $sortOrder = 'DESC';

        // Act
        $query = new RaffleAllocationQuery()
            ->withRaffleId($raffleId)
            ->paginate($limit, $offset)
            ->sortBy($sortField, $sortOrder);

        // Assert
        self::assertSame($raffleId, $query->raffleId);
        self::assertSame($limit, $query->limit);
        self::assertSame($offset, $query->offset);
        self::assertSame($sortField, $query->sortField);
        self::assertSame($sortOrder, $query->sortOrder);
    }

    #[Test]
    public function it_cannot_set_the_sort_field_by_a_non_existing_field(): void
    {
        // Arrange
        $query = new RaffleAllocationQuery();

        // Act
        $query = $query->sortBy('INVALID');

        // Assert
        self::assertNull($query->sortField);
    }
}

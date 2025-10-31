<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Domain\Projection\Raffle\V1;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleQuery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RaffleQueryTest extends TestCase
{
    #[Test]
    public function it_can_create_a_query(): void
    {
        // Arrange
        $name = 'name';
        $prize = 'prize';
        $status = 'status';
        $startAt = Clock::fromString('2025-01-01 01:01:01');
        $closeAt = Clock::fromString('2025-02-02 02:02:02');
        $drawAt = Clock::fromString('2025-03-03 03:03:03');
        $limit = 10;
        $offset = 5;
        $sortField = 'name';
        $sortOrder = 'DESC';

        // Act
        $query = new RaffleQuery()
            ->withName($name)
            ->withPrize($prize)
            ->withStatus($status)
            ->withStartAt($startAt)
            ->withCloseAt($closeAt)
            ->withDrawAt($drawAt)
            ->paginate($limit, $offset)
            ->sortBy($sortField, $sortOrder);

        // Assert
        self::assertSame($name, $query->name);
        self::assertSame($prize, $query->prize);
        self::assertSame($status, $query->status);
        self::assertSame($startAt, $query->startAt);
        self::assertSame($closeAt, $query->closeAt);
        self::assertSame($drawAt, $query->drawAt);
        self::assertSame($limit, $query->limit);
        self::assertSame($offset, $query->offset);
        self::assertSame($sortField, $query->sortField);
        self::assertSame($sortOrder, $query->sortOrder);
    }

    #[Test]
    public function it_cannot_set_the_sort_field_by_a_non_existing_field(): void
    {
        // Arrange
        $query = new RaffleQuery();

        // Act
        $query = $query->sortBy('INVALID');

        // Assert
        self::assertNull($query->sortField);
    }

    #[Test]
    public function it_cannot_set_the_sort_order_with_an_invalid_value(): void
    {
        // Arrange
        $query = new RaffleQuery();

        // Act
        $query = $query->sortBy('name', 'INVALID');

        // Assert
        self::assertSame('ASC', $query->sortOrder);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\RaffleAllocation\V1;

use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocation;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationQuery;

use function array_slice;

final class InMemoryRaffleAllocationProjectionRepository implements RaffleAllocationProjectionRepositoryInterface
{
    /** @var RaffleAllocation[] */
    public array $raffleAllocations = [];

    public function store(RaffleAllocation $raffleAllocation): void
    {
        $this->raffleAllocations[] = $raffleAllocation;
    }

    public function query(RaffleAllocationQuery $query): array
    {
        $raffleAllocations = $this->raffleAllocations;

        if ($query->raffleId !== null) {
            $raffleAllocations = array_filter(
                $raffleAllocations,
                static fn (RaffleAllocation $raffleAllocation) => $raffleAllocation->raffleId === $query->raffleId,
            );
        }

        if ($query->sortField !== null) {
            usort($raffleAllocations, static function (RaffleAllocation $a, RaffleAllocation $b) use ($query) {
                $field = $query->sortField;

                return $a->$field <=> $b->$field; // @phpstan-ignore-line  property.dynamicName
            });
        }

        if ($query->limit !== null) {
            $raffleAllocations = array_slice($raffleAllocations, $query->offset, $query->limit);
        }

        return $raffleAllocations;
    }
}

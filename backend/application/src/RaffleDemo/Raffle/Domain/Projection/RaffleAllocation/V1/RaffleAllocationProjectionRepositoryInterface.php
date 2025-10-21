<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1;

interface RaffleAllocationProjectionRepositoryInterface
{
    public function store(RaffleAllocation $raffleAllocation): void;

    /** @return RaffleAllocation[] */
    public function query(RaffleAllocationQuery $query): array;
}

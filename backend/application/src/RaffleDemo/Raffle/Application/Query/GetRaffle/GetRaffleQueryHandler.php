<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffle;

use App\Framework\Application\Query\QueryHandlerInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationQuery;

final readonly class GetRaffleQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private RaffleProjectionRepositoryInterface $raffleProjectionRepository,
        private RaffleAllocationProjectionRepositoryInterface $raffleAllocationProjectionRepository,
    ) {
    }

    public function __invoke(GetRaffleQuery $query): GetRaffleResult
    {
        $raffle = $this->raffleProjectionRepository->getById($query->id);

        if ($raffle === null) {
            return GetRaffleResult::fromNull();
        }

        $allocations = $this->raffleAllocationProjectionRepository->query(new RaffleAllocationQuery()->withRaffleId($raffle->id));

        return GetRaffleResult::fromRaffle($raffle, ...$allocations);
    }
}

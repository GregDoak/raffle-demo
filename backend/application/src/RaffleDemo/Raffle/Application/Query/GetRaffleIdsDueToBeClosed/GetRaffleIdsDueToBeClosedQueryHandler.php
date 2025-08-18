<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed;

use App\Framework\Application\Query\QueryHandlerInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjectionRepositoryInterface;

final readonly class GetRaffleIdsDueToBeClosedQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private RaffleProjectionRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetRaffleIdsDueToBeClosedQuery $query): GetRaffleIdsDueToBeClosedResult
    {
        $raffles = $this->repository->getRafflesDueToBeClosed($query->closeAt);

        return GetRaffleIdsDueToBeClosedResult::fromRaffles(...$raffles);
    }
}

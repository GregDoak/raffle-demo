<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed;

use App\Framework\Application\Query\QueryHandlerInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleQueryFactory;

final readonly class GetRaffleIdsDueToBeClosedQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private RaffleProjectionRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetRaffleIdsDueToBeClosedQuery $query): GetRaffleIdsDueToBeClosedResult
    {
        $raffles = $this->repository->query(RaffleQueryFactory::getRafflesDueToBeClosedQuery($query->closeAt));

        return GetRaffleIdsDueToBeClosedResult::fromRaffles(...$raffles);
    }
}

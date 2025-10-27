<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeStarted;

use App\Framework\Application\Query\QueryHandlerInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleQueryFactory;

final readonly class GetRaffleIdsDueToBeStartedQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private RaffleProjectionRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetRaffleIdsDueToBeStartedQuery $query): GetRaffleIdsDueToBeStartedResult
    {
        $raffles = $this->repository->query(RaffleQueryFactory::getRafflesDueToBeStartedQuery($query->startAt));

        return GetRaffleIdsDueToBeStartedResult::fromRaffles(...$raffles);
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeStarted;

use App\Framework\Application\Query\QueryHandlerInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjectionRepositoryInterface;

final readonly class GetRaffleIdsDueToBeStartedQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private RaffleProjectionRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetRaffleIdsDueToBeStartedQuery $query): GetRaffleIdsDueToBeStartedResult
    {
        $raffles = $this->repository->getRafflesDueToBeStarted($query->startAt);

        return GetRaffleIdsDueToBeStartedResult::fromRaffles(...$raffles);
    }
}

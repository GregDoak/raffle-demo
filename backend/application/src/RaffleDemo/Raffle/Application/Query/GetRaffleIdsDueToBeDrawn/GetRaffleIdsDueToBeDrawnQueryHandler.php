<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn;

use App\Framework\Application\Query\QueryHandlerInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjectionRepositoryInterface;

final readonly class GetRaffleIdsDueToBeDrawnQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private RaffleProjectionRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetRaffleIdsDueToBeDrawnQuery $query): GetRaffleIdsDueToBeDrawnResult
    {
        $raffles = $this->repository->getRafflesDueToBeDrawn($query->drawAt);

        return GetRaffleIdsDueToBeDrawnResult::fromRaffles(...$raffles);
    }
}

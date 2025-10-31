<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffles;

use App\Framework\Application\Query\QueryHandlerInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleQuery;

use function count;

final readonly class GetRafflesQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private RaffleProjectionRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetRafflesQuery $query): GetRafflesResult
    {
        $raffles = $this->repository->query(
            new RaffleQuery(
                name: $query->name,
                prize: $query->prize,
                status: $query->status,
            )
                ->sortBy($query->sortField, $query->sortOrder),
        );

        $total = count($raffles);
        $raffles = array_splice($raffles, offset: $query->offset, length: $query->limit);

        return GetRafflesResult::fromRaffles($total, ...$raffles);
    }
}

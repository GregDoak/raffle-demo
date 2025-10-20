<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1;

use DateTimeInterface;

final readonly class RaffleQueryFactory
{
    public static function getRafflesDueToBeStartedQuery(
        DateTimeInterface $now,
        ?int $limit = null,
        int $offset = 0,
    ): RaffleQuery {
        $query = new RaffleQuery()
            ->withStatus('created')
            ->withStartAt($now)
            ->sortBy('startAt', 'ASC');

        if ($limit !== null) {
            $query = $query->paginate($limit, $offset);
        }

        return $query;
    }

    public static function getRafflesDueToBeClosedQuery(
        DateTimeInterface $now,
        ?int $limit = null,
        int $offset = 0,
    ): RaffleQuery {
        $query = new RaffleQuery()
            ->withStatus('started')
            ->withCloseAt($now)
            ->sortBy('closeAt', 'ASC');

        if ($limit !== null) {
            $query = $query->paginate($limit, $offset);
        }

        return $query;
    }

    public static function getRafflesDueToBeDrawnQuery(
        DateTimeInterface $now,
        ?int $limit = null,
        int $offset = 0,
    ): RaffleQuery {
        $query = new RaffleQuery()
            ->withStatus('closed')
            ->withDrawAt($now)
            ->sortBy('drawAt', 'ASC');

        if ($limit !== null) {
            $query = $query->paginate($limit, $offset);
        }

        return $query;
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffle;

use App\Framework\Application\Query\QueryInterface;

final readonly class GetRaffleQuery implements QueryInterface
{
    private function __construct(
        public string $id,
    ) {
    }

    public static function create(string $id): GetRaffleQuery
    {
        return new self(
            id: $id,
        );
    }
}

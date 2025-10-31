<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffles;

use App\Framework\Application\Query\QueryInterface;

final readonly class GetRafflesQuery implements QueryInterface
{
    private function __construct(
        public ?string $name,
        public ?string $prize,
        public ?string $status,
        public int $limit,
        public int $offset,
        public string $sortField,
        public string $sortOrder,
    ) {
    }

    public static function create(
        ?string $name,
        ?string $prize,
        ?string $status,
        int $limit,
        int $offset,
        string $sortField,
        string $sortOrder,
    ): GetRafflesQuery {
        return new self(
            $name,
            $prize,
            $status,
            $limit,
            $offset,
            $sortField,
            $sortOrder,
        );
    }
}

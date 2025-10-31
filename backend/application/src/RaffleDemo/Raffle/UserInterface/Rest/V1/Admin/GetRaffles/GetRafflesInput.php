<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\GetRaffles;

final readonly class GetRafflesInput
{
    public function __construct(
        public ?string $name,
        public ?string $prize,
        public ?string $status,
        public int $limit,
        public int $offset,
        public string $sortField,
        public string $sortOrder,
    ) {
    }
}

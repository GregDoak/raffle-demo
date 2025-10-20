<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1;

interface RaffleProjectionRepositoryInterface
{
    public function store(Raffle $raffle): void;

    public function getById(string $id): ?Raffle;

    /** @return Raffle[] */
    public function query(RaffleQuery $query): array;
}

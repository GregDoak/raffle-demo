<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\Raffle;

interface RaffleProjectionRepositoryInterface
{
    public function store(Raffle $raffle): void;

    public function getById(string $id): ?Raffle;
}

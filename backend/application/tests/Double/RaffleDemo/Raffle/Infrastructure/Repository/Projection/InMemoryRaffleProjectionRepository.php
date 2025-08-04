<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection;

use App\RaffleDemo\Raffle\Domain\Projection\Raffle\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjectionRepositoryInterface;

final class InMemoryRaffleProjectionRepository implements RaffleProjectionRepositoryInterface
{
    /** @var Raffle[] */
    public array $raffles = [];

    public function store(Raffle $raffle): void
    {
        $this->raffles[$raffle->id] = $raffle;
    }

    public function getById(string $id): ?Raffle
    {
        return $this->raffles[$id] ?? null;
    }
}

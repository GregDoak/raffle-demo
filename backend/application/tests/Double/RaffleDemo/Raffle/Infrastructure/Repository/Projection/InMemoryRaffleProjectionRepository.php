<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection;

use App\RaffleDemo\Raffle\Domain\Projection\Raffle\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjectionRepositoryInterface;
use DateTimeInterface;

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

    public function getRafflesDueToBeStarted(DateTimeInterface $now): array
    {
        return array_filter($this->raffles, fn (Raffle $raffle) => $raffle->startAt <= $now && $raffle->startedAt === null);
    }

    public function getRafflesDueToBeClosed(DateTimeInterface $now): array
    {
        return array_filter($this->raffles, fn (Raffle $raffle) => $raffle->closeAt <= $now && $raffle->closedAt === null);
    }

    public function getRafflesDueToBeDrawn(DateTimeInterface $now): array
    {
        return array_filter($this->raffles, fn (Raffle $raffle) => $raffle->drawAt <= $now && $raffle->drawnAt === null);
    }
}

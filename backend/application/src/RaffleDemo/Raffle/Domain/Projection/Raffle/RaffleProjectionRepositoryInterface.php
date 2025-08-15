<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\Raffle;

use DateTimeInterface;

interface RaffleProjectionRepositoryInterface
{
    public function store(Raffle $raffle): void;

    public function getById(string $id): ?Raffle;

    /** @return Raffle[] */
    public function getRafflesDueToBeStarted(DateTimeInterface $now): array;

    /** @return Raffle[] */
    public function getRafflesDueToBeClosed(DateTimeInterface $now): array;

    /** @return Raffle[] */
    public function getRafflesDueToBeDrawn(DateTimeInterface $now): array;
}

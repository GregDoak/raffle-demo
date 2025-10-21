<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1;

interface RaffleWinnerProjectionRepositoryInterface
{
    public function store(RaffleWinner $raffleWinner): void;

    /** @return RaffleWinner[] */
    public function query(RaffleWinnerQuery $query): array;
}

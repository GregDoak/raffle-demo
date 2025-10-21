<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\RaffleWinner\V1;

use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinner;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinnerProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleWinner\V1\RaffleWinnerQuery;

use function array_slice;

final class InMemoryRaffleWinnerProjectionRepository implements RaffleWinnerProjectionRepositoryInterface
{
    /** @var RaffleWinner[] */
    public array $raffleWinners = [];

    public function store(RaffleWinner $raffleWinner): void
    {
        $this->raffleWinners[] = $raffleWinner;
    }

    public function query(RaffleWinnerQuery $query): array
    {
        $raffleWinners = $this->raffleWinners;

        if ($query->raffleId !== null) {
            $raffleWinners = array_filter(
                $raffleWinners,
                static fn (RaffleWinner $raffleWinner) => $raffleWinner->raffleId === $query->raffleId,
            );
        }

        if ($query->drawnAt !== null) {
            $raffleWinners = array_filter(
                $raffleWinners,
                static fn (RaffleWinner $raffleWinner) => $raffleWinner->drawnAt <= $query->drawnAt,
            );
        }

        if ($query->sortField !== null) {
            usort($raffleWinners, static function (RaffleWinner $a, RaffleWinner $b) use ($query) {
                $field = $query->sortField;

                return $a->$field <=> $b->$field; // @phpstan-ignore-line  property.dynamicName
            });
        }

        if ($query->limit !== null) {
            $raffleWinners = array_slice($raffleWinners, $query->offset, $query->limit);
        }

        return $raffleWinners;
    }
}

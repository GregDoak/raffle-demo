<?php

declare(strict_types=1);

namespace App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Repository\Projection\Raffle\V1;

use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleQuery;

use function array_slice;

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

    public function query(RaffleQuery $query): array
    {
        $raffles = $this->raffles;

        if ($query->name !== null) {
            $raffles = array_filter(
                $raffles,
                static fn (Raffle $raffle) => str_contains($raffle->name, $query->name),
            );
        }

        if ($query->prize !== null) {
            $raffles = array_filter(
                $raffles,
                static fn (Raffle $raffle) => str_contains($raffle->prize, $query->prize),
            );
        }

        if ($query->status !== null) {
            $raffles = array_filter(
                $raffles,
                static fn (Raffle $raffle) => $raffle->status === $query->status,
            );
        }

        if ($query->startAt !== null) {
            $raffles = array_filter(
                $raffles,
                static fn (Raffle $raffle) => $raffle->status === 'created' && $raffle->startAt <= $query->startAt,
            );
        }

        if ($query->closeAt !== null) {
            $raffles = array_filter(
                $raffles,
                static fn (Raffle $raffle) => $raffle->status === 'started' && $raffle->closeAt <= $query->closeAt,
            );
        }

        if ($query->drawAt !== null) {
            $raffles = array_filter(
                $raffles,
                static fn (Raffle $raffle) => $raffle->status === 'closed' && $raffle->drawAt <= $query->drawAt,
            );
        }

        if ($query->sortField !== null) {
            usort($raffles, static function (Raffle $a, Raffle $b) use ($query) {
                $field = $query->sortField;

                return $a->$field <=> $b->$field; // @phpstan-ignore-line  property.dynamicName
            });
        }

        if ($query->limit !== null) {
            $raffles = array_slice($raffles, $query->offset, $query->limit);
        }

        return $raffles;
    }
}

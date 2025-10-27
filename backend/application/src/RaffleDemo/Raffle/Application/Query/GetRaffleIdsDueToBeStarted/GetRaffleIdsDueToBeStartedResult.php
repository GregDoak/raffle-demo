<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeStarted;

use App\Framework\Application\Query\ResultInterface;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\Raffle;

use function array_key_exists;

final readonly class GetRaffleIdsDueToBeStartedResult implements ResultInterface
{
    /** @param array<string, string> $ids */
    private function __construct(
        private array $ids,
    ) {
    }

    public static function fromRaffles(Raffle ...$raffles): self
    {
        $ids = [];

        foreach ($raffles as $raffle) {
            if (array_key_exists($raffle->id, $ids)) {
                continue;
            }

            $ids[$raffle->id] = $raffle->id;
        }

        return new self($ids);
    }

    /** @return string[] */
    public function getIds(): array
    {
        return array_values($this->ids);
    }
}

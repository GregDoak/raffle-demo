<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\CreateRaffle;

final readonly class CreateRaffleInput
{
    /** @param array{amount: int, currency: string} $ticketPrice */
    public function __construct(
        public string $name,
        public string $prize,
        public string $startAt,
        public string $closeAt,
        public string $drawAt,
        public int $totalTickets,
        public array $ticketPrice,
    ) {
    }
}

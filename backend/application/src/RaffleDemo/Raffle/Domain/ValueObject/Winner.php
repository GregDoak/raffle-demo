<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\ValueObject;

use App\RaffleDemo\Raffle\Domain\Exception\InvalidWinnerException;

final readonly class Winner
{
    private function __construct(
        private TicketAllocation $ticketAllocation,
        private int $ticketNumber,
    ) {
        if ($this->ticketNumber < 1) {
            throw InvalidWinnerException::fromLessThan1();
        }
    }

    public static function fromWinningTicketAllocation(TicketAllocation $ticketAllocation, int $ticketNumber): self
    {
        return new self($ticketAllocation, $ticketNumber);
    }

    /**
     * @infection-ignore-all
     * @param array{ticketAllocation: array{quantity: int, allocatedTo: string, allocatedAt: string}, ticketNumber: ?int} $array
     */
    public static function fromArray(array $array): self
    {
        return new self(TicketAllocation::fromArray($array['ticketAllocation']), $array['ticketNumber'] ?? 0);
    }

    /** @return array{ticketAllocation: array{quantity: int, allocatedTo: string, allocatedAt: string}, ticketNumber: int} */
    public function toArray(): array
    {
        return [
            'ticketAllocation' => $this->ticketAllocation->toArray(),
            'ticketNumber' => $this->ticketNumber,
        ];
    }

    public function toTicketAllocation(): TicketAllocation
    {
        return $this->ticketAllocation;
    }
}

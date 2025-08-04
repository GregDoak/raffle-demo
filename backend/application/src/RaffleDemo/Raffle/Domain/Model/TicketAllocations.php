<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Domain\Model;

use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketAllocationsException;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;

use function array_key_exists;

final class TicketAllocations
{
    /** @var array<string, TicketAllocation> */
    private array $ticketAllocations;

    /** @var array<int, string> */
    private array $tickets;

    private int $currentTicketNumber = 0;

    public int $numberOfTicketsAllocated = 0;

    private function __construct()
    {
        $this->ticketAllocations = [];
    }

    public static function fromNew(): self
    {
        return new self();
    }

    public function has(TicketAllocation $ticketAllocation): bool
    {
        return array_key_exists($ticketAllocation->hash, $this->ticketAllocations);
    }

    public function addTicketAllocation(TicketAllocation $ticketAllocation): void
    {
        $this->ticketAllocations[$ticketAllocation->hash] = $ticketAllocation;
        for ($index = 1; $index <= $ticketAllocation->quantity; ++$index) {
            $this->tickets[++$this->currentTicketNumber] = $ticketAllocation->hash;
        }
        $this->numberOfTicketsAllocated += $ticketAllocation->quantity;
    }

    public function drawTicketAllocationFromTicketNumber(int $ticketNumber): TicketAllocation
    {
        if (array_key_exists($ticketNumber, $this->tickets) === false) {
            throw InvalidTicketAllocationsException::fromCannotDrawUnallocatedTicket();
        }

        $ticketAllocationHash = $this->tickets[$ticketNumber];

        return $this->ticketAllocations[$ticketAllocationHash];
    }

    public function drawWinningTicketNumber(): int
    {
        /* @infection-ignore-all */
        return random_int(1, $this->currentTicketNumber);
    }
}

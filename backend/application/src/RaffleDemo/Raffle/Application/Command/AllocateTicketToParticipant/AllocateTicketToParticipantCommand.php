<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant;

use App\Framework\Application\Command\CommandInterface;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use DateTimeInterface;

final readonly class AllocateTicketToParticipantCommand implements CommandInterface
{
    private function __construct(
        public RaffleAggregateId $id,
        public TicketAllocation $ticketAllocation,
    ) {
    }

    public static function create(
        string $id,
        int $ticketAllocatedQuantity,
        string $ticketAllocatedTo,
        DateTimeInterface $ticketAllocatedAt,
    ): self {
        return new self(
            RaffleAggregateId::fromString($id),
            TicketAllocation::from(
                quantity: $ticketAllocatedQuantity,
                allocatedTo: $ticketAllocatedTo,
                allocatedAt: $ticketAllocatedAt,
            ),
        );
    }
}

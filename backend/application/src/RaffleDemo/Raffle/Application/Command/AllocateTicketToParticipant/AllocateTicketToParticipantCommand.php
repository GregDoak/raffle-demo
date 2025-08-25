<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant;

use App\Framework\Application\Command\CommandInterface;
use App\Framework\Application\Exception\ValidationException;
use App\Framework\Domain\Exception\InvalidAggregateIdException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketAllocationException;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\ValueObject\TicketAllocation;
use DateTimeInterface;

use function count;

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
        $errors = [];

        try {
            $id = RaffleAggregateId::fromString($id);
        } catch (InvalidAggregateIdException $exception) {
            $errors[] = $exception->getMessage();
        }

        try {
            $ticketAllocation = TicketAllocation::from(
                quantity: $ticketAllocatedQuantity,
                allocatedTo: $ticketAllocatedTo,
                allocatedAt: $ticketAllocatedAt,
            );
        } catch (InvalidTicketAllocationException $exception) {
            $errors[] = $exception->getMessage();
        }

        if (count($errors) > 0) {
            throw ValidationException::fromErrors($errors);
        }

        return new self(
            id: $id, // @phpstan-ignore argument.type
            ticketAllocation: $ticketAllocation, // @phpstan-ignore variable.undefined
        );
    }
}

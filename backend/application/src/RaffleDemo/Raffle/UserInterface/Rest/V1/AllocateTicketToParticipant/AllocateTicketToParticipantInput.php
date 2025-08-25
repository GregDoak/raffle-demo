<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\AllocateTicketToParticipant;

use DateTimeInterface;

final readonly class AllocateTicketToParticipantInput
{
    public function __construct(
        public string $id,
        public int $ticketAllocatedQuantity,
        public string $ticketAllocatedTo,
        public DateTimeInterface $ticketAllocatedAt,
    ) {
    }
}

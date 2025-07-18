<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\Application\Command\CreateRaffle;

use App\Framework\Application\CommandHandlerInterface;
use App\RaffleDemo\Raffle\Domain\Model\Raffle;

final readonly class CreateRaffleCommandHandler implements CommandHandlerInterface
{
    public function __invoke(CreateRaffleCommand $command): void
    {
        // TODO - will add a transactions boundary and repository for persistence
        $raffle = Raffle::create(
            $command->id,
            $command->name,
            $command->prize,
            $command->startAt,
            $command->closeAt,
            $command->drawAt,
            $command->totalTickets,
            $command->ticketPrice,
            $command->created,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\CreateRaffle;

use App\Framework\Application\Command\CommandBusInterface;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rest/v1/raffles', methods: ['POST'])]
final readonly class CreateRaffleController
{
    public function __invoke(
        CommandBusInterface $commandBus,
        CreateRaffleInput $input,
    ): JsonResponse {
        $command = CreateRaffleCommand::create(
            $input->name,
            $input->prize,
            $input->startAt,
            $input->closeAt,
            $input->drawAt,
            $input->totalTickets,
            $input->ticketPrice,
            $input->createdBy,
        );

        /* @infection-ignore-all */
        $commandBus->dispatchSync($command);

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}

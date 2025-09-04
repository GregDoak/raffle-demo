<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\AllocateTicketToParticipant;

use App\Framework\Application\Command\CommandBusInterface;
use App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant\AllocateTicketToParticipantCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rest/v1/raffles/{id}/allocate', methods: ['POST'])]
final readonly class AllocateTicketToParticipantController
{
    public function __invoke(
        CommandBusInterface $commandBus,
        AllocateTicketToParticipantInput $input,
    ): JsonResponse {
        $command = AllocateTicketToParticipantCommand::create(
            $input->id,
            $input->ticketAllocatedQuantity,
            $input->ticketAllocatedTo,
            $input->ticketAllocatedAt,
        );

        /* @infection-ignore-all */
        $commandBus->dispatchSync($command);

        return new JsonResponse(status: Response::HTTP_OK);
    }
}

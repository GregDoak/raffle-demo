<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\CreateRaffle;

use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommandHandler;
use App\RaffleDemo\Raffle\Domain\Repository\RaffleEventStoreRepository;
use App\Tests\Double\Framework\Domain\Repository\InMemoryEventStore;
use App\Tests\Double\Framework\Domain\Repository\TransactionBoundarySpy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rest/v1/raffle', methods: ['POST'])]
final readonly class CreateRaffleController
{
    public function __invoke(
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
        new CreateRaffleCommandHandler(
            new TransactionBoundarySpy(),
            new RaffleEventStoreRepository(new InMemoryEventStore()),
        )->__invoke($command);

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}

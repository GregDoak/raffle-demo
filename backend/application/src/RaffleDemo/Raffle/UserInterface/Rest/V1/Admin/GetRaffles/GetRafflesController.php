<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\GetRaffles;

use App\Framework\Application\Query\QueryBusInterface;
use App\Framework\Infrastructure\Symfony\Security\Admin\AdminUser;
use App\RaffleDemo\Raffle\Application\Query\GetRaffles\GetRafflesQuery;
use App\RaffleDemo\Raffle\Application\Query\GetRaffles\GetRafflesResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/rest/v1/admin/raffles', methods: ['GET'])]
final readonly class GetRafflesController
{
    public function __invoke(
        #[CurrentUser] AdminUser $user,
        QueryBusInterface $queryBus,
        GetRafflesInput $input,
    ): JsonResponse {
        $query = GetRafflesQuery::create(
            $input->name,
            $input->prize,
            $input->status,
            $input->limit,
            $input->offset,
            $input->sortField,
            $input->sortOrder,
        );

        /** @var GetRafflesResult $result */
        $result = $queryBus->query($query);

        return new JsonResponse(data: ['data' => $result->raffles, 'total' => $result->total], status: Response::HTTP_OK);
    }
}

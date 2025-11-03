<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\GetRaffle;

use App\Framework\Application\Query\QueryBusInterface;
use App\Framework\Infrastructure\Symfony\Security\Admin\AdminUser;
use App\RaffleDemo\Raffle\Application\Query\GetRaffle\GetRaffleQuery;
use App\RaffleDemo\Raffle\Application\Query\GetRaffle\GetRaffleResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/rest/v1/admin/raffles/{id}', methods: ['GET'])]
final readonly class GetRaffleController
{
    public function __invoke(
        #[CurrentUser] AdminUser $user,
        QueryBusInterface $queryBus,
        string $id,
    ): JsonResponse {
        /** @var GetRaffleResult $result */
        $result = $queryBus->query(GetRaffleQuery::create($id));

        if ($result->raffle === null) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse(data: ['data' => $result->raffle], status: Response::HTTP_OK);
    }
}

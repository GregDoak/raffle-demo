<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\GetRaffles;

use App\Framework\Infrastructure\Symfony\Security\Admin\AdminUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/rest/v1/admin/raffles', methods: ['GET'])]
final readonly class GetRafflesController
{
    public function __invoke(
        #[CurrentUser] AdminUser $user,
    ): JsonResponse {
        return new JsonResponse(data: ['data' => [], 'total' => 0], status: Response::HTTP_OK);
    }
}

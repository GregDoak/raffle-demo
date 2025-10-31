<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\GetRaffles;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class GetRafflesInputResolver implements ValueResolverInterface
{
    /** @return GetRafflesInput[] */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== GetRafflesInput::class) {
            return [];
        }

        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(10, $request->query->getInt('limit', 10));
        $offset = ($page - 1) * $limit;

        yield new GetRafflesInput(
            name: $request->query->get('name'),
            prize: $request->query->get('prize'),
            status: $request->query->get('status'),
            limit: $limit,
            offset: $offset,
            sortField: $request->query->getString('sort', 'startAt'),
            sortOrder: $request->query->getString('order', 'ASC'),
        );
    }
}

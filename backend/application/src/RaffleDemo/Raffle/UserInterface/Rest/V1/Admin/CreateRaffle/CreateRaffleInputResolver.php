<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\CreateRaffle;

use App\Foundation\Serializer\JsonSerializer;
use App\Framework\UserInterface\Exception\ValidationException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class CreateRaffleInputResolver implements ValueResolverInterface
{
    public function __construct(
        private CreateRaffleInputValidator $validator,
    ) {
    }

    /**
     * @infection-ignore-all
     * @return CreateRaffleInput[]
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== CreateRaffleInput::class) {
            return [];
        }

        $input = JsonSerializer::deserialize($request->getContent());
        $result = $this->validator->validate($input); // @phpstan-ignore-line argument.type

        if ($result->isValid() === false && $result->error() instanceof ValidationError) {
            throw ValidationException::fromErrors(new ErrorFormatter()->format($result->error())); // @phpstan-ignore argument.type
        }

        yield new CreateRaffleInput(
            $input['name'], // @phpstan-ignore-line argument.type
            $input['prize'], // @phpstan-ignore-line argument.type
            $input['startAt'], // @phpstan-ignore-line argument.type
            $input['closeAt'], // @phpstan-ignore-line argument.type
            $input['drawAt'], // @phpstan-ignore-line argument.type
            $input['totalTickets'], // @phpstan-ignore-line argument.type
            $input['ticketPrice'], // @phpstan-ignore-line argument.type
        );
    }
}

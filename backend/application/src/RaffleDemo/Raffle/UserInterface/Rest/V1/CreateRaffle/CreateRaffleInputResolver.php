<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\CreateRaffle;

use App\Foundation\Serializer\JsonSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Exception\ValidationFailedException;

use function count;

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

        $violations = $this->validator->validate($input); // @phpstan-ignore-line argument.type

        if (count($violations) > 0) {
            throw new ValidationFailedException($input, $violations);
        }

        $input = new CreateRaffleInput(
            $input['name'], // @phpstan-ignore-line argument.type
            $input['prize'], // @phpstan-ignore-line argument.type
            $input['startAt'], // @phpstan-ignore-line argument.type
            $input['closeAt'], // @phpstan-ignore-line argument.type
            $input['drawAt'], // @phpstan-ignore-line argument.type
            $input['totalTickets'], // @phpstan-ignore-line argument.type
            $input['ticketPrice'], // @phpstan-ignore-line argument.type
            $input['createdBy'], // @phpstan-ignore-line argument.type
        );

        return [$input];
    }
}

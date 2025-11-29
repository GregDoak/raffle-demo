<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\AllocateTicketToParticipant;

use App\Foundation\Clock\Clock;
use App\Foundation\Serializer\JsonSerializer;
use App\Framework\UserInterface\Exception\ValidationException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class AllocateTicketToParticipantInputResolver implements ValueResolverInterface
{
    public function __construct(
        private AllocateTicketToParticipantValidator $validator,
    ) {
    }

    /**
     * @infection-ignore-all
     * @return AllocateTicketToParticipantInput[]
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== AllocateTicketToParticipantInput::class) {
            return [];
        }

        $input = JsonSerializer::deserialize($request->getContent());
        $input = array_merge((array) $input, ['id' => $request->attributes->get('id', '')]);
        $result = $this->validator->validate($input);

        if ($result->isValid() === false && $result->error() instanceof ValidationError) {
            throw ValidationException::fromErrors(new ErrorFormatter()->format($result->error())); // @phpstan-ignore argument.type
        }

        yield new AllocateTicketToParticipantInput(
            id: $input['id'], // @phpstan-ignore-line argument.type
            ticketAllocatedQuantity: $input['quantity'], // @phpstan-ignore-line argument.type
            ticketAllocatedTo: $input['allocatedTo'], // @phpstan-ignore-line argument.type
            ticketAllocatedAt: Clock::fromString($input['allocatedAt']), // @phpstan-ignore-line argument.type
        );
    }
}

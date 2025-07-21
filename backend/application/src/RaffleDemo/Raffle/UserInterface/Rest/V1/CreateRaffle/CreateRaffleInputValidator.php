<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\CreateRaffle;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

final readonly class CreateRaffleInputValidator
{
    /** @param array{
     *     name?: ?mixed,
     *     prize?: ?mixed,
     *     startAt?: ?mixed,
     *     closeAt?: ?mixed,
     *     drawAt?: ?mixed,
     *     totalTickets?: ?mixed,
     *     ticketPrice?: array{amount?: ?mixed, currency?: mixed},
     *     createdBy?: ?mixed
     * } $input
     */
    public function validate(array $input): ConstraintViolationListInterface
    {
        $validator = Validation::createValidator();

        /** @infection-ignore-all  */
        $constraints = new Collection([
            'name' => [new Assert\Required(), new Assert\Type('string')],
            'prize' => [new Assert\Required(), new Assert\Type('string')],
            'startAt' => [new Assert\Required(), new Assert\Type('string')],
            'closeAt' => [new Assert\Required(), new Assert\Type('string')],
            'drawAt' => [new Assert\Required(), new Assert\Type('string')],
            'totalTickets' => [new Assert\Required(), new Assert\Type('int')],
            'ticketPrice' => new Collection([
                'amount' => [new Assert\Required(), new Assert\Type('int')],
                'currency' => [new Assert\Required(), new Assert\Type('string')],
            ],
                allowExtraFields: false,
                allowMissingFields: false,
            ),
            'createdBy' => [new Assert\Required(), new Assert\Type('string')],
        ],
            allowExtraFields: false,
            allowMissingFields: false,
        );

        return $validator->validate($input, $constraints);
    }
}

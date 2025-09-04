<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\CreateRaffle;

use Opis\JsonSchema\Helper;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;

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
    public function validate(array $input): ValidationResult
    {
        $json = Helper::toJSON($input);

        return new Validator(
            max_errors: 100,
            stop_at_first_error: false,
        )->validate($json, Helper::toJSON(self::getSchema())); // @phpstan-ignore argument.type
    }

    private static function getSchema(): array // @phpstan-ignore missingType.iterableValue
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'prize' => ['type' => 'string'],
                'startAt' => ['type' => 'string', 'format' => 'date-time'],
                'closeAt' => ['type' => 'string', 'format' => 'date-time'],
                'drawAt' => ['type' => 'string', 'format' => 'date-time'],
                'totalTickets' => ['type' => 'integer'],
                'ticketPrice' => [
                    'type' => 'object',
                    'properties' => ['amount' => ['type' => 'integer'], 'currency' => ['type' => 'string']],
                    'required' => ['amount', 'currency'],
                ],
                'createdBy' => ['type' => 'string'],
            ],
            'required' => [
                'name',
                'prize',
                'startAt',
                'closeAt',
                'drawAt',
                'totalTickets',
                'ticketPrice',
                'createdBy',
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\AllocateTicketToParticipant;

use Opis\JsonSchema\Helper;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;

final readonly class AllocateTicketToParticipantValidator
{
    /** @param array{
     *     id?: ?mixed,
     *     quantity?: ?mixed,
     *     allocatedTo?: ?mixed,
     *     allocatedAt?: ?mixed,
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
                'id' => ['type' => 'string'],
                'quantity' => ['type' => 'integer', 'minimum' => 1],
                'allocatedTo' => ['type' => 'string'],
                'allocatedAt' => ['type' => 'string', 'format' => 'date-time'],
            ],
            'required' => [
                'id',
                'quantity',
                'allocatedTo',
                'allocatedAt',
            ],
        ];
    }
}

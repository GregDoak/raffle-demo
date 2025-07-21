<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\UserInterface\Rest\V1\CreateRaffle;

use App\RaffleDemo\Raffle\UserInterface\Rest\V1\CreateRaffle\CreateRaffleInputValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CreateRaffleInputValidatorTest extends TestCase
{
    #[Test]
    public function it_succeeds_when_validating_a_valid_input(): void
    {
        // Arrange
        $input = [
            'name' => 'string',
            'prize' => 'string',
            'startAt' => 'string',
            'closeAt' => 'string',
            'drawAt' => 'string',
            'totalTickets' => 100,
            'ticketPrice' => [
                'amount' => 1000,
                'currency' => 'string',
            ],
            'createdBy' => 'string',
        ];
        $validator = new CreateRaffleInputValidator();

        // Act
        $violations = $validator->validate($input);

        // Assert
        self::assertEmpty($violations);
    }

    #[Test]
    public function it_fails_when_validating_an_input_with_wrong_types(): void
    {
        // Arrange
        $input = [
            'name' => 1,
            'prize' => 1,
            'startAt' => 1,
            'closeAt' => 1,
            'drawAt' => 1,
            'totalTickets' => 'string',
            'ticketPrice' => [
                'amount' => 'string',
                'currency' => 1,
            ],
            'createdBy' => 1,
        ];
        $validator = new CreateRaffleInputValidator();

        // Act
        $violations = $validator->validate($input);

        // Assert
        self::assertCount(9, $violations);
    }

    #[Test]
    public function it_fails_when_validating_an_input_with_missing_fields(): void
    {
        // Arrange
        $input = [
            'name' => 'string',
            'prize' => 'string',
            'startAt' => 'string',
            'closeAt' => 'string',
            'drawAt' => 'string',
            'totalTickets' => 100,
            'ticketPrice' => [
                'amount' => 1000,
                'currency' => 'string',
            ],
            'createdBy' => 'string',
        ];
        unset($input['ticketPrice']['currency'], $input['createdBy']);
        $validator = new CreateRaffleInputValidator();

        // Act
        $violations = $validator->validate($input);

        // Assert
        self::assertCount(2, $violations);
    }

    #[Test]
    public function it_fails_when_validating_an_input_with_extra_fields(): void
    {
        // Arrange
        $input = [
            'name' => 'string',
            'prize' => 'string',
            'startAt' => 'string',
            'closeAt' => 'string',
            'drawAt' => 'string',
            'totalTickets' => 100,
            'ticketPrice' => [
                'amount' => 1000,
                'currency' => 'string',
                'extraField' => 'string',
            ],
            'createdBy' => 'string',
            'extraField' => 'string',
        ];
        $validator = new CreateRaffleInputValidator();

        // Act
        $violations = $validator->validate($input);

        // Assert
        self::assertCount(2, $violations);
    }
}

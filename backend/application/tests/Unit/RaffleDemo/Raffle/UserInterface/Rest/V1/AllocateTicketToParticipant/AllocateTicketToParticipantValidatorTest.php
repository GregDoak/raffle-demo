<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\UserInterface\Rest\V1\AllocateTicketToParticipant;

use App\RaffleDemo\Raffle\UserInterface\Rest\V1\AllocateTicketToParticipant\AllocateTicketToParticipantValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AllocateTicketToParticipantValidatorTest extends TestCase
{
    #[Test]
    public function it_succeeds_when_validating_a_valid_input(): void
    {
        // Arrange
        $input = [
            'id' => 'string',
            'quantity' => 1,
            'allocatedTo' => 'string',
            'allocatedAt' => '1970-01-01 00:00:00',
        ];
        $validator = new AllocateTicketToParticipantValidator();

        // Act
        $result = $validator->validate($input);

        // Assert
        self::assertTrue($result->isValid());
    }

    #[Test]
    public function it_fails_when_validating_an_input_with_wrong_types(): void
    {
        // Arrange
        $input = [
            'id' => 1,
            'quantity' => 'string',
            'allocatedTo' => 1,
            'allocatedAt' => 1,
        ];
        $validator = new AllocateTicketToParticipantValidator();

        // Act
        $result = $validator->validate($input);

        // Assert
        self::assertFalse($result->isValid());
        self::assertCount(4, $result->error()?->subErrors() ?? []);
    }

    #[Test]
    public function it_fails_when_validating_an_input_with_missing_fields(): void
    {
        // Arrange
        $input = [
            'id' => 'string',
            'quantity' => 1,
            'allocatedTo' => 'string',
            'allocatedAt' => '1970-01-01 00:00:00',
        ];
        unset($input['allocatedAt']);
        $validator = new AllocateTicketToParticipantValidator();

        // Act
        $result = $validator->validate($input);

        // Assert
        self::assertFalse($result->isValid());
        self::assertTrue($result->hasError());
    }
}

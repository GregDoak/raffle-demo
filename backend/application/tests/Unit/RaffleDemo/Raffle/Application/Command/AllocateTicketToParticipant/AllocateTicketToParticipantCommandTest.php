<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant;

use App\Foundation\Clock\Clock;
use App\Framework\Application\Exception\ValidationException;
use App\Framework\Domain\Exception\InvalidAggregateIdException;
use App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant\AllocateTicketToParticipantCommand;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketAllocationException;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

final class AllocateTicketToParticipantCommandTest extends TestCase
{
    /** @param array{
     *     id?: string,
     *     ticketAllocatedQuantity?: int,
     *     ticketAllocatedTo?: string,
     *     ticketAllocatedAt?: string,
     * } $fields
     */
    #[Test, DataProvider('it_fails_when_given_invalid_input_scenarios')]
    public function it_fails_when_given_invalid_input(array $fields, ValidationException $expectedException): void
    {
        // Arrange
        $id = $fields['id'] ?? RaffleAggregateId::fromNew()->toString();
        $ticketAllocatedQuantity = $fields['ticketAllocatedQuantity'] ?? 100;
        $ticketAllocatedTo = $fields['ticketAllocatedTo'] ?? 'ticket-allocated-to';
        $ticketAllocatedAt = Clock::fromString($fields['ticketAllocatedAt'] ?? '2025-01-01 00:00:00');
        $exception = null;

        // Act
        try {
            AllocateTicketToParticipantCommand::create(
                id: $id,
                ticketAllocatedQuantity: $ticketAllocatedQuantity,
                ticketAllocatedTo: $ticketAllocatedTo,
                ticketAllocatedAt: $ticketAllocatedAt,
            );
        } catch (Throwable $exception) {
        }

        // Assert
        self::assertEquals($expectedException, $exception);
    }

    public static function it_fails_when_given_invalid_input_scenarios(): Generator
    {
        yield 'invalid id field' => [
            'fields' => ['id' => ''],
            'expectedException' => ValidationException::fromErrors(
                [InvalidAggregateIdException::fromInvalidId()->getMessage()],
            ),
        ];

        yield 'invalid ticket allocated field' => [
            'fields' => ['ticketAllocatedQuantity' => 0],
            'expectedException' => ValidationException::fromErrors(
                [InvalidTicketAllocationException::fromInvalidQuantity()->getMessage()],
            ),
        ];
    }
}

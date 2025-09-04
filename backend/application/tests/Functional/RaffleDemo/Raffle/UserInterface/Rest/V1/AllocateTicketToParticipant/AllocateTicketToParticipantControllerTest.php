<?php

declare(strict_types=1);

namespace App\Tests\Functional\RaffleDemo\Raffle\UserInterface\Rest\V1\AllocateTicketToParticipant;

use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\Foundation\Serializer\JsonSerializer;
use App\Framework\Application\Command\CommandBusInterface;
use App\Tests\Context\RaffleDemo\Raffle\Application\Command\RaffleApplicationContext;
use App\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

final class AllocateTicketToParticipantControllerTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function it_allocates_a_ticket_to_a_raffle(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $commandBus = self::getContainer()->get(CommandBusInterface::class);
        $commandBus->dispatchSync($command = RaffleApplicationContext::getCreateRaffleCommand());
        $commandBus->dispatchSync(
            RaffleApplicationContext::getStartRaffleCommand(
                id: $command->id->toString(),
                startedAt: $command->startAt->toDateTime(),
                startedBy: 'system',
            ),
        );

        $input = [
            'quantity' => 1,
            'allocatedTo' => 'participant',
            'allocatedAt' => '2025-01-02T12:00:00Z',
        ];

        // Act
        $this->client->request(
            method: 'POST',
            uri: sprintf('/rest/v1/raffles/%s/allocate', $command->id->toString()),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: JsonSerializer::serialize($input),
        );

        // Assert
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    #[Test]
    public function it_fails_with_http_400_code_when_given_an_invalid_input(): void
    {
        $input = [
            'quantity' => 'INVALID',
            'allocatedTo' => 1,
            'allocatedAt' => 1,
        ];

        // Act
        $this->client->request(
            method: 'POST',
            uri: sprintf('/rest/v1/raffles/%s/allocate', 'INVALID'),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: JsonSerializer::serialize($input),
        );

        // Assert
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }

    #[Test]
    public function it_fails_with_http_404_code_when_given_an_unknown_id(): void
    {
        $input = [
            'quantity' => 1,
            'allocatedTo' => 'participant',
            'allocatedAt' => '2025-01-02T12:00:00Z',
        ];

        // Act
        $this->client->request(
            method: 'POST',
            uri: sprintf('/rest/v1/raffles/%s/allocate', 'MISSING'),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: JsonSerializer::serialize($input),
        );

        // Assert
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }
}

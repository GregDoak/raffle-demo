<?php

declare(strict_types=1);

namespace App\Tests\Functional\RaffleDemo\Raffle\UserInterface\Rest\V1\CreateRaffle;

use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\Foundation\Serializer\JsonSerializer;
use App\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

final class CreateRaffleControllerTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function it_creates_a_raffle(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $input = [
            'name' => 'raffle-name',
            'prize' => 'raffle-prize',
            'startAt' => '2025-01-01 00:00:00',
            'closeAt' => '2025-01-02 00:00:00',
            'drawAt' => '2025-01-02 00:00:00',
            'totalTickets' => 100,
            'ticketPrice' => [
                'amount' => 1000,
                'currency' => 'GBP',
            ],
            'createdBy' => 'user',
        ];

        // Act
        $this->client->request(
            method: 'POST',
            uri: '/rest/v1/raffle',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: JsonSerializer::serialize($input),
        );

        // Assert
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    #[Test]
    public function it_fails_with_http_400_code_when_given_an_invalid_input(): void
    {
        $input = [
            'name' => 1,
            'prize' => 1,
            'startAt' => 'INVALID',
            'closeAt' => 'INVALID',
            'drawAt' => 'INVALID',
            'totalTickets' => 'INVALID',
            'ticketPrice' => [
                'amount' => 'INVALID',
                'currency' => 1,
            ],
            'createdBy' => 1,
        ];

        // Act
        $this->client->request(
            method: 'POST',
            uri: '/rest/v1/raffle',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: JsonSerializer::serialize($input),
        );

        // Assert
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }
}

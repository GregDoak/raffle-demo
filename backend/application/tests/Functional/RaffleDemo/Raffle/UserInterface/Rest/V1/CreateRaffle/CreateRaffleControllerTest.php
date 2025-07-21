<?php

declare(strict_types=1);

namespace App\Tests\Functional\RaffleDemo\Raffle\UserInterface\Rest\V1\CreateRaffle;

use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\Foundation\Serializer\JsonSerializer;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CreateRaffleControllerTest extends WebTestCase
{
    #[Test]
    public function it_creates_a_raffle(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $client = self::createClient();
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
        $client->request(
            method: 'POST',
            uri: '/rest/v1/raffle',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: JsonSerializer::serialize($input),
        );

        // Assert
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}

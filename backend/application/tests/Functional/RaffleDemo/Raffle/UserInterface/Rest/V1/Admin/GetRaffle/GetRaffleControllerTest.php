<?php

declare(strict_types=1);

namespace App\Tests\Functional\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\GetRaffle;

use App\Foundation\Clock\Clock;
use App\RaffleDemo\Raffle\Application\Query\GetRaffle\GetRaffleResult;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocation;
use App\RaffleDemo\Raffle\Domain\Projection\RaffleAllocation\V1\RaffleAllocationProjectionRepositoryInterface;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionDomainContext;
use App\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

final class GetRaffleControllerTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function it_returns_a_raffle(): void
    {
        // Arrange
        $raffleRepository = self::getContainer()->get(RaffleProjectionRepositoryInterface::class);
        $raffleAllocationRepository = self::getContainer()->get(RaffleAllocationProjectionRepositoryInterface::class);

        $raffle = RaffleProjectionDomainContext::create(RaffleAggregateId::fromNew()->toString(), '1');
        $raffleAllocation = new RaffleAllocation(raffleId: $raffle->id, hash: 'hash-1', allocatedAt: Clock::now(), allocatedTo: 'allocated-to', quantity: 1, lastOccurredAt: Clock::now());

        $raffleRepository->store($raffle);
        $raffleAllocationRepository->store($raffleAllocation);

        $expectedResponse = [
            'data' => GetRaffleResult::fromRaffle($raffle, $raffleAllocation)->raffle,
        ];

        // Act
        $this->client->request(
            method: 'GET',
            uri: '/rest/v1/admin/raffles/'.$raffle->id,
            server: ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => $this->getAdminUserToken()],
        );

        // Assert
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertSame($expectedResponse, json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR));
    }

    #[Test]
    public function it_fails_with_http_401_code_when_given_an_input_with_missing_credentials(): void
    {
        // Act
        $this->client->request(
            method: 'GET',
            uri: '/rest/v1/admin/raffles/ANY_RAFFLE_ID',
            server: ['CONTENT_TYPE' => 'application/json'],
        );

        // Assert
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }

    #[Test]
    public function it_fails_with_http_404_code_when_given_a_non_existing_id(): void
    {
        // Act
        $this->client->request(
            method: 'GET',
            uri: '/rest/v1/admin/raffles/08aeb9d7-44a6-4f8b-950a-4ee1b9c9e31a',
            server: ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => $this->getAdminUserToken()],
        );

        // Assert
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }
}

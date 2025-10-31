<?php

declare(strict_types=1);

namespace App\Tests\Functional\RaffleDemo\Raffle\UserInterface\Rest\V1\Admin\GetRaffles;

use App\RaffleDemo\Raffle\Application\Query\GetRaffles\GetRafflesResult;
use App\RaffleDemo\Raffle\Domain\Model\RaffleAggregateId;
use App\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionRepositoryInterface;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\V1\RaffleProjectionDomainContext;
use App\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

final class GetRafflesControllerTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function it_returns_an_empty_list(): void
    {
        // Arrange
        $expectedResponse = [
            'data' => [],
            'total' => 0,
        ];

        // Act
        $this->client->request(
            method: 'GET',
            uri: '/rest/v1/admin/raffles',
            server: ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => $this->getAdminUserToken()],
        );

        // Assert
        $response = $this->client->getResponse();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertSame($expectedResponse, json_decode((string) $response->getContent(), true, flags: JSON_THROW_ON_ERROR));
    }

    #[Test]
    public function it_returns_a_list(): void
    {
        // Arrange
        $repository = self::getContainer()->get(RaffleProjectionRepositoryInterface::class);
        $raffle1 = RaffleProjectionDomainContext::create(RaffleAggregateId::fromNew()->toString(), '1');
        $raffle2 = RaffleProjectionDomainContext::create(RaffleAggregateId::fromNew()->toString(), '2');
        $raffle3 = RaffleProjectionDomainContext::create(RaffleAggregateId::fromNew()->toString(), '3');
        $repository->store($raffle1);
        $repository->store($raffle2);
        $repository->store($raffle3);

        $expectedResponse = [
            'data' => GetRafflesResult::fromRaffles(3, $raffle1, $raffle2, $raffle3)->raffles,
            'total' => 3,
        ];

        // Act
        $this->client->request(
            method: 'GET',
            uri: '/rest/v1/admin/raffles?sort=status&order=ASC',
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
            uri: '/rest/v1/admin/raffles',
            server: ['CONTENT_TYPE' => 'application/json'],
        );

        // Assert
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertResponseHeaderSame('Content-Type', 'application/problem+json');
    }
}

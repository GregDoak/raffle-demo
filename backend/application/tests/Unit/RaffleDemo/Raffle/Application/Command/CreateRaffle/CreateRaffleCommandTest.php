<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Command\CreateRaffle;

use App\Framework\Application\Exception\ValidationException;
use App\Framework\Domain\Exception\InvalidDateTimeException;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommand;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidCreatedException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidNameException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidPrizeException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTicketPriceException;
use App\RaffleDemo\Raffle\Domain\Exception\InvalidTotalTicketsException;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

use function sprintf;

final class CreateRaffleCommandTest extends TestCase
{
    /** @param array{
     *     name?: string,
     *     prize?: string,
     *     startAt?: string,
     *     closeAt?: string,
     *     drawAt?: string,
     *     totalTickets?: int,
     *     ticketPrice?: array{amount: int, currency: string},
     *     createdBy?: string,
     * } $fields
     */
    #[Test, DataProvider('it_fails_when_given_an_invalid_input_scenarios')]
    public function it_fails_when_given_an_invalid_input(array $fields, ValidationException $expectedException): void
    {
        // Arrange
        $name = $fields['name'] ?? 'raffle-name';
        $prize = $fields['prize'] ?? 'raffle-prize';
        $startAt = $fields['startAt'] ?? '2025-01-02 00:00:00';
        $closeAt = $fields['closeAt'] ?? '2025-01-03 00:00:00';
        $drawAt = $fields['drawAt'] ?? '2025-01-03 00:00:00';
        $totalTickets = $fields['totalTickets'] ?? 100;
        $ticketPrice = $fields['ticketPrice'] ?? ['amount' => 1000, 'currency' => 'GBP'];
        $createdBy = $fields['createdBy'] ?? 'user';
        $exception = null;

        // Act
        try {
            CreateRaffleCommand::create(
                name: $name,
                prize: $prize,
                startAt: $startAt,
                closeAt: $closeAt,
                drawAt: $drawAt,
                totalTickets: $totalTickets,
                ticketPrice: $ticketPrice,
                createdBy: $createdBy,
            );
        } catch (Throwable $exception) {
        }

        // Assert
        self::assertEquals($expectedException, $exception);
    }

    public static function it_fails_when_given_an_invalid_input_scenarios(): Generator
    {
        yield 'single invalid field' => [
            'fields' => ['name' => ''],
            'expectedException' => ValidationException::fromErrors(
                [InvalidNameException::fromEmptyName()->getMessage()],
            ),
        ];

        yield 'all invalid fields' => [
            'fields' => [
                'name' => '',
                'prize' => '',
                'startAt' => '',
                'closeAt' => 'INVALID',
                'drawAt' => '',
                'totalTickets' => 0,
                'ticketPrice' => ['amount' => -1000, 'currency' => ''],
                'createdBy' => '',
            ],
            'expectedException' => ValidationException::fromErrors(
                [
                    InvalidNameException::fromEmptyName()->getMessage(),
                    InvalidPrizeException::fromEmptyPrize()->getMessage(),
                    sprintf(InvalidDateTimeException::fromEmptyDateTime()->template, 'start at'),
                    sprintf(InvalidDateTimeException::fromInvalidDateTime()->template, 'close at'),
                    sprintf(InvalidDateTimeException::fromEmptyDateTime()->template, 'draw at'),
                    InvalidTotalTicketsException::fromLessThan1()->getMessage(),
                    InvalidTicketPriceException::fromNegativeAmount()->getMessage(),
                    InvalidCreatedException::fromEmptyBy()->getMessage(),
                ],
            ),
        ];
    }
}

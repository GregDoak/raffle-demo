<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\Application\Command\CreateRaffle;

use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommandHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CreateRaffleCommandHandlerTest extends TestCase
{
    private CreateRaffleCommandHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new CreateRaffleCommandHandler();
    }

    #[Test]
    public function it_creates_a_raffle(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $command = CreateRaffleCommand::create(
            name: 'raffle-demo',
            prize: 'raffle-prize',
            startAt: '2025-01-02 00:00:00',
            closeAt: '2025-01-03 00:00:00',
            drawAt: '2025-01-03 00:00:00',
            totalTickets: 100,
            ticketPrice: ['amount' => 1000, 'currency' => 'GBP'],
            createdBy: 'user',
        );

        // Act
        $this->handler->__invoke($command);

        // Assert
        // TODO - will be able to perform assertions on the in memory store once added
        self::expectNotToPerformAssertions();
    }
}

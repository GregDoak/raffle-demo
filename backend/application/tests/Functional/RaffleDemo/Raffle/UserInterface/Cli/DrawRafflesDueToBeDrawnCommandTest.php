<?php

declare(strict_types=1);

namespace App\Tests\Functional\RaffleDemo\Raffle\UserInterface\Cli;

use App\Foundation\Clock\Clock;
use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\Framework\Application\Command\CommandBusInterface;
use App\Tests\Context\RaffleDemo\Raffle\Application\Command\RaffleApplicationContext;
use App\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;

final class DrawRafflesDueToBeDrawnCommandTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function it_executes_the_raffle_due_to_be_drawn_command(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $command = $this->application->find('raffle:cron:draw');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Found 0 Raffles to draw', $output);
        self::assertStringContainsString('Dispatched 0 Raffles to draw in 0.00000 seconds', $output);
    }

    #[Test]
    public function it_draws_a_raffle_when_a_raffle_is_due_to_be_drawn(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $commandBus = self::getContainer()->get(CommandBusInterface::class);
        $command = RaffleApplicationContext::getCreateRaffleCommand();
        $id = $command->id;
        $commandBus->dispatchSync($command);
        $commandBus->dispatchSync(RaffleApplicationContext::getStartRaffleCommand(
            id: $id->toString(),
            startedAt: Clock::fromString('2025-01-02 00:00:00'),
            startedBy: 'system',
        ));
        $commandBus->dispatchSync(RaffleApplicationContext::getAllocateTicketToParticipantCommand(
            id: $id->toString(),
            ticketAllocatedQuantity: 10,
            ticketAllocatedTo: 'participant',
            ticketAllocatedAt: Clock::fromString('2025-01-02 00:00:00'),
        ));
        $commandBus->dispatchSync(RaffleApplicationContext::getCloseRaffleCommand(
            id: $id->toString(),
            closedAt: Clock::fromString('2025-01-03 00:00:00'),
            closedBy: 'system',
        ));

        ClockProvider::set(new MockClock('2025-01-03 00:00:00'));
        $command = $this->application->find('raffle:cron:draw');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Found 1 Raffles to draw', $output);
        self::assertStringContainsString('Dispatched 1 Raffles to draw in 0.00000 seconds', $output);
    }
}

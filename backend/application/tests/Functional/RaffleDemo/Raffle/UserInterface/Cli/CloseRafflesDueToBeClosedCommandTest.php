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

final class CloseRafflesDueToBeClosedCommandTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function it_executes_the_raffle_due_to_be_closed_command(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $command = $this->application->find('raffle:cron:close');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Found 0 Raffles to close', $output);
        self::assertStringContainsString('Dispatched 0 Raffles to close in 0.00000 seconds', $output);
    }

    #[Test]
    public function it_closes_a_raffle_when_a_raffle_is_due_to_be_closed(): void
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

        ClockProvider::set(new MockClock('2025-01-03 00:00:00'));
        $command = $this->application->find('raffle:cron:close');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Found 1 Raffles to close', $output);
        self::assertStringContainsString('Dispatched 1 Raffles to close in 0.00000 seconds', $output);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Functional\RaffleDemo\Raffle\UserInterface\Cli;

use App\Foundation\Clock\ClockProvider;
use App\Foundation\Clock\MockClock;
use App\Framework\Application\Command\CommandBusInterface;
use App\Tests\Context\RaffleDemo\Raffle\Application\Command\RaffleApplicationContext;
use App\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;

final class StartRafflesDueToBeStartedCommandTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function it_executes_the_raffle_due_to_be_started_command(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $command = $this->application->find('raffle:cron:start');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Found 0 Raffles to start', $output);
        self::assertStringContainsString('Dispatched 0 Raffles to start in 0.00000 seconds', $output);
    }

    #[Test]
    public function it_starts_a_raffle_when_a_raffle_due_to_be_started(): void
    {
        // Arrange
        ClockProvider::set(new MockClock('2025-01-01 00:00:00'));
        $commandBus = self::getContainer()->get(CommandBusInterface::class);
        $commandBus->dispatchSync(RaffleApplicationContext::getCreateRaffleCommand());

        ClockProvider::set(new MockClock('2025-01-02 00:00:00'));
        $command = $this->application->find('raffle:cron:start');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Found 1 Raffles to start', $output);
        self::assertStringContainsString('Dispatched 1 Raffles to start in 0.00000 seconds', $output);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\UserInterface\Cli;

use App\Foundation\Uuid\Uuid;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn\GetRaffleIdsDueToBeDrawnResult;
use App\RaffleDemo\Raffle\UserInterface\Cli\DrawRafflesDueToBeDrawnCommand;
use App\Tests\Context\RaffleDemo\Raffle\Domain\Projection\Raffle\RaffleProjectionDomainContext;
use App\Tests\Double\Framework\Application\Command\CommandBusSpy;
use App\Tests\Double\Framework\Application\Query\QueryBusDummy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DrawRafflesDueToBeDrawnCommandTest extends TestCase
{
    #[Test]
    public function it_does_not_dispatch_any_commands_when_there_are_no_raffles_to_be_drawn(): void
    {
        // Arrange
        $commandBus = new CommandBusSpy();
        $queryBus = new QueryBusDummy(GetRaffleIdsDueToBeDrawnResult::fromRaffles());
        $command = new DrawRafflesDueToBeDrawnCommand($commandBus, $queryBus);
        $io = new SymfonyStyle(new ArrayInput([]), new NullOutput());

        // Act
        $command->__invoke($io);

        // Assert
        self::assertCount(0, $commandBus->commands);
    }

    #[Test]
    public function it_dispatches_commands_when_where_are_raffles_due_to_be_drawn(): void
    {
        // Arrange
        $raffles = [
            RaffleProjectionDomainContext::create(id: Uuid::v7()),
            RaffleProjectionDomainContext::create(id: Uuid::v7()),
            RaffleProjectionDomainContext::create(id: Uuid::v7()),
        ];
        $commandBus = new CommandBusSpy();
        $queryBus = new QueryBusDummy(GetRaffleIdsDueToBeDrawnResult::fromRaffles(...$raffles));
        $command = new DrawRafflesDueToBeDrawnCommand($commandBus, $queryBus);
        $io = new SymfonyStyle(new ArrayInput([]), new NullOutput());

        // Act
        $command->__invoke($io);

        // Assert
        self::assertCount(3, $commandBus->commands);
    }
}

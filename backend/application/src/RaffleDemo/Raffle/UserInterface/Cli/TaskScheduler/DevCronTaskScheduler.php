<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Cli\TaskScheduler;

use App\RaffleDemo\Raffle\UserInterface\Cli\StartRafflesDueToBeStartedCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

/** @infection-ignore-all  */
#[When('dev')]
#[AsCronTask('* * * * *')]
final readonly class DevCronTaskScheduler
{
    public function __construct(
        private StartRafflesDueToBeStartedCommand $startRafflesDueToBeStartedCommand,
    ) {
    }

    public function __invoke(): void
    {
        $io = new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());

        $this->startRafflesDueToBeStartedCommand->__invoke($io);
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Cli;

use App\Foundation\Clock\Clock;
use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\Application\Query\QueryBusInterface;
use App\RaffleDemo\Raffle\Application\Command\CloseRaffle\CloseRaffleCommand;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed\GetRaffleIdsDueToBeClosedQuery;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeClosed\GetRaffleIdsDueToBeClosedResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;
use function sprintf;

#[AsCommand(name: 'raffle:cron:close', description: 'Closes raffles that are due to be closed.')]
final readonly class CloseRafflesDueToBeClosedCommand
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $closeAt = Clock::now();

        /** @var GetRaffleIdsDueToBeClosedResult $result */
        $result = $this->queryBus->query(GetRaffleIdsDueToBeClosedQuery::create($closeAt));

        $io->info(sprintf('Found %d Raffles to close', count($result->getIds())));

        $dispatched = 0;
        foreach ($result->getIds() as $id) {
            $this->commandBus->dispatchSync(
                CloseRaffleCommand::create(id: $id, closedAt: $closeAt, closedBy: 'system'),
            );
            ++$dispatched;
        }

        $completeAt = Clock::now();
        $difference = $completeAt->diff($closeAt);

        $io->info(
            sprintf('Dispatched %d Raffles to close in %.5f seconds', $dispatched, $difference->s + $difference->f),
        );

        return Command::SUCCESS;
    }
}

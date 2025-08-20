<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Cli;

use App\Foundation\Clock\Clock;
use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\Application\Query\QueryBusInterface;
use App\RaffleDemo\Raffle\Application\Command\DrawPrize\DrawPrizeCommand;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn\GetRaffleIdsDueToBeDrawnQuery;
use App\RaffleDemo\Raffle\Application\Query\GetRaffleIdsDueToBeDrawn\GetRaffleIdsDueToBeDrawnResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;
use function sprintf;

#[AsCommand(name: 'raffle:cron:draw', description: 'Draws raffles that are due to be drawn.')]
final readonly class DrawRafflesDueToBeDrawnCommand
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $drawAt = Clock::now();

        /** @var GetRaffleIdsDueToBeDrawnResult $result */
        $result = $this->queryBus->query(GetRaffleIdsDueToBeDrawnQuery::create($drawAt));

        $io->info(sprintf('Found %d Raffles to draw', count($result->getIds())));

        $dispatched = 0;
        foreach ($result->getIds() as $id) {
            $this->commandBus->dispatchSync(
                DrawPrizeCommand::create(id: $id, drawnAt: $drawAt, drawnBy: 'system'),
            );
            ++$dispatched;
        }

        $completeAt = Clock::now();
        $difference = $completeAt->diff($drawAt);

        $io->info(
            sprintf('Dispatched %d Raffles to draw in %.5f seconds', $dispatched, $difference->s + $difference->f),
        );

        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Cli\Fixture;

use App\Foundation\Clock\Clock;
use App\Framework\Application\Command\CommandBusInterface;
use App\RaffleDemo\Raffle\Application\Command\AllocateTicketToParticipant\AllocateTicketToParticipantCommand;
use App\RaffleDemo\Raffle\Application\Command\CloseRaffle\CloseRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\CreateRaffle\CreateRaffleCommand;
use App\RaffleDemo\Raffle\Application\Command\DrawPrize\DrawPrizeCommand;
use App\RaffleDemo\Raffle\Application\Command\StartRaffle\StartRaffleCommand;
use DateTimeInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;

use function in_array;
use function sprintf;

/** @infection-ignore-all  */
#[When('dev')]
#[AsCommand(name: 'raffle:fixtures:create', description: 'Create a raffle fixtures for a given number and state.')]
final readonly class CreateRaffleFixturesCommand
{
    private const array ALLOWED_STATES = ['create', 'start', 'close', 'draw', 'all'];
    private string $actionedBy;

    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
        $this->actionedBy = 'fixture-'.rand(1, 99999);
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'The number of records to create')] int $records = 1,
        #[Option(description: 'The state to create the raffles (leave blank to create for all states)')] string $state = 'all',
    ): int {
        if ($records <= 0) {
            $io->error('The number of records requested must be greater than 0.');

            return Command::FAILURE;
        }

        if (in_array($state, self::ALLOWED_STATES, strict: true) === false) {
            $io->error(
                sprintf(
                    'The state "%s" is not allowed, only one of "%s" are allowed.',
                    $state,
                    implode(', ', self::ALLOWED_STATES),
                ),
            );

            return Command::FAILURE;
        }

        for ($index = 0; $index < $records; ++$index) {
            switch ($state) {
                case 'create':
                    $this->createRaffle();
                    break;

                case 'start':
                    $this->startRaffle();
                    break;

                case 'close':
                    $this->closeRaffle();
                    break;

                case 'draw':
                    $this->drawRaffle();
                    break;

                case 'all':
                    $this->createRaffle();
                    $this->startRaffle();
                    $this->closeRaffle();
                    $this->drawRaffle();
                    break;
            }

            // Force object cleanup after each iteration
            gc_collect_cycles();
        }

        $io->success(sprintf('Created %d records for state "%s"', $records, $state));

        return Command::SUCCESS;
    }

    private function createRaffle(): void
    {
        $command = $this->getCreateRaffleCommand();

        $this->commandBus->dispatchSync($command);
    }

    private function startRaffle(): void
    {
        $command = $this->getCreateRaffleCommand();
        $id = $command->id->toString();
        $startAt = $command->startAt;

        $this->commandBus->dispatchSync($command);
        $this->commandBus->dispatchSync($this->getStartRaffleCommand($id, $startAt->toDateTime()));
    }

    private function closeRaffle(): void
    {
        $command = $this->getCreateRaffleCommand();
        $id = $command->id->toString();
        $startAt = $command->startAt;
        $closeAt = $command->closeAt;

        $this->commandBus->dispatchSync($command);
        $this->commandBus->dispatchSync($this->getStartRaffleCommand($id, $startAt->toDateTime()));
        $this->commandBus->dispatchSync($this->getCloseRaffleCommand($id, $closeAt->toDateTime()));
    }

    private function drawRaffle(): void
    {
        $command = $this->getCreateRaffleCommand();
        $id = $command->id->toString();
        $totalTickets = $command->totalTickets;
        $startAt = $command->startAt;
        $closeAt = $command->closeAt;
        $drawAt = $command->drawAt;

        $this->commandBus->dispatchSync($command);

        $command = $this->getStartRaffleCommand($id, $startAt->toDateTime());
        $startedAt = $command->started->at;

        $this->commandBus->dispatchSync($command);
        $remainingTickets = $totalTickets->toInt();

        for ($index = 0; $index < $totalTickets->toInt(); ++$index) {
            $quantity = rand(1, $remainingTickets);
            $this->commandBus->dispatchSync(
                $this->getAllocateTicketToParticipantCommand($id, $quantity, $startedAt, $closeAt->toDateTime()),
            );

            $remainingTickets -= $quantity;

            if ($remainingTickets === 0 || rand(1, 5) === 1) {
                break;
            }
        }
        $this->commandBus->dispatchSync($this->getCloseRaffleCommand($id, $closeAt->toDateTime()));
        $this->commandBus->dispatchSync($this->getDrawPrizeCommand($id, $drawAt->toDateTime()));
    }

    private function getCreateRaffleCommand(): CreateRaffleCommand
    {
        $now = Clock::now();
        $startAt = $now->getTimestamp() + rand(1, 60 * 60 * 48);
        $closeAt = $startAt + rand(60 * 60 * 24, 60 * 60 * 72);
        $drawAt = $closeAt + rand(0, 60 * 60 * 48);

        return CreateRaffleCommand::create(
            name: 'raffle-name-'.rand(1, 99999),
            prize: 'raffle-prize-'.rand(1, 99999),
            startAt: Clock::fromTimestamp($startAt)->format('Y-m-d H:i:s'),
            closeAt: Clock::fromTimestamp($closeAt)->format('Y-m-d H:i:s'),
            drawAt: Clock::fromTimestamp($drawAt)->format('Y-m-d H:i:s'),
            totalTickets: rand(10, 100),
            ticketPrice: ['amount' => 0, 'currency' => 'GBP'],
            createdBy: $this->actionedBy,
        );
    }

    private function getStartRaffleCommand(string $id, DateTimeInterface $startAt): StartRaffleCommand
    {
        return StartRaffleCommand::create(
            id: $id,
            startedAt: Clock::fromTimestamp($startAt->getTimestamp() + rand(1, 60 * 60 * 2)),
            startedBy: $this->actionedBy,
        );
    }

    private function getCloseRaffleCommand(string $id, DateTimeInterface $closeAt): CloseRaffleCommand
    {
        return CloseRaffleCommand::create(
            id: $id,
            closedAt: Clock::fromTimestamp($closeAt->getTimestamp() + rand(1, 60 * 60 * 2)),
            closedBy: $this->actionedBy,
        );
    }

    private function getAllocateTicketToParticipantCommand(
        string $id,
        int $quantity,
        DateTimeInterface $startedAt,
        DateTimeInterface $closeAt,
    ): AllocateTicketToParticipantCommand {
        return AllocateTicketToParticipantCommand::create(
            id: $id,
            ticketAllocatedQuantity: $quantity,
            ticketAllocatedTo: 'participant-'.rand(1, 99999),
            ticketAllocatedAt: Clock::fromTimestamp(rand($startedAt->getTimestamp() + 1, $closeAt->getTimestamp())),
        );
    }

    private function getDrawPrizeCommand(string $id, DateTimeInterface $drawAt): DrawPrizeCommand
    {
        return DrawPrizeCommand::create(
            id: $id,
            drawnAt: Clock::fromTimestamp($drawAt->getTimestamp() + rand(1, 60 * 60 * 2)),
            drawnBy: $this->actionedBy,
        );
    }
}

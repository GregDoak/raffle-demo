<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger;

use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\Application\Command\CommandInterface;
use App\Framework\Application\Command\Exception\CommandNotRegisteredException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;

final readonly class SymfonyCommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function dispatchSync(CommandInterface $command): void
    {
        $this->dispatch($command, new TransportNamesStamp(['sync']));
    }

    private function dispatch(CommandInterface $command, StampInterface $stamp): void
    {
        try {
            $this->commandBus->dispatch($command, [$stamp]);
        } catch (NoHandlerForMessageException) {
            throw new CommandNotRegisteredException($command);
        } catch (HandlerFailedException $exception) {
            while ($exception instanceof HandlerFailedException) {
                $exception = $exception->getPrevious();
            }

            if ($exception !== null) {
                throw $exception;
            }
        }
    }
}

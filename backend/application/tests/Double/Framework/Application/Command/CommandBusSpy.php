<?php

declare(strict_types=1);

namespace App\Tests\Double\Framework\Application\Command;

use App\Framework\Application\Command\CommandBusInterface;
use App\Framework\Application\Command\CommandInterface;

final class CommandBusSpy implements CommandBusInterface
{
    /** @var CommandInterface[] */
    public private(set) array $commands = [] {
        get {
            return $this->commands;
        }
    }

    public function dispatchSync(CommandInterface $command): void
    {
        $this->commands = array_merge($this->commands, [$command]);
    }
}

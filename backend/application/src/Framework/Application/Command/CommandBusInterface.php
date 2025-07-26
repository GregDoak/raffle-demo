<?php

declare(strict_types=1);

namespace App\Framework\Application\Command;

interface CommandBusInterface
{
    public function dispatchSync(CommandInterface $command): void;
}

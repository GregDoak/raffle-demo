<?php

declare(strict_types=1);

namespace App\Framework\Application\Command\Exception;

use App\Framework\Application\Command\CommandInterface;
use RuntimeException;

use function sprintf;

final class CommandNotRegisteredException extends RuntimeException
{
    public function __construct(CommandInterface $command)
    {
        parent::__construct(sprintf('The command "%s" is not registered with a handler', $command::class));
    }
}

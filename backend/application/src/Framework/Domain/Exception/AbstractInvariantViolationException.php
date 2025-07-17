<?php

declare(strict_types=1);

namespace App\Framework\Domain\Exception;

use RuntimeException;

abstract class AbstractInvariantViolationException extends RuntimeException
{
    final private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function fromMessage(string $message): static
    {
        return new static($message);
    }
}

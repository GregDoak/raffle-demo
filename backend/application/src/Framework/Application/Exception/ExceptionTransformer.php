<?php

declare(strict_types=1);

namespace App\Framework\Application\Exception;

use App\Framework\Domain\Exception\AbstractInvariantViolationException;
use App\Framework\Domain\Exception\AggregateNotFoundException;
use Throwable;

final readonly class ExceptionTransformer implements ExceptionTransformerInterface
{
    public function transform(Throwable $exception): Throwable
    {
        return match ($exception::class) {
            AbstractInvariantViolationException::class => ValidationException::fromError($exception->getMessage()),
            AggregateNotFoundException::class => ResourceNotFoundException::fromId($exception->id),
            default => $exception,
        };
    }
}

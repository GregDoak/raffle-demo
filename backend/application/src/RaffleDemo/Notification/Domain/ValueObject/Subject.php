<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\ValueObject;

use App\Framework\Domain\ValueObject\AbstractString;
use App\RaffleDemo\Notification\Domain\Exception\InvalidSubjectException;

final readonly class Subject extends AbstractString
{
    protected function __construct(string $value)
    {
        if ($value === '') {
            throw InvalidSubjectException::fromEmptySubject();
        }

        parent::__construct($value);
    }
}

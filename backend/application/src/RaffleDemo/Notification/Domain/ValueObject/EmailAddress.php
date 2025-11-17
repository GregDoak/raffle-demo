<?php

declare(strict_types=1);

namespace App\RaffleDemo\Notification\Domain\ValueObject;

use App\RaffleDemo\Notification\Domain\Exception\InvalidEmailAddressException;

final readonly class EmailAddress extends AbstractRecipient
{
    protected function __construct(string $value)
    {
        if ($value === '') {
            throw InvalidEmailAddressException::fromEmptyEmailAddress();
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw InvalidEmailAddressException::fromInvalidEmailAddress();
        }

        parent::__construct($value);
    }
}

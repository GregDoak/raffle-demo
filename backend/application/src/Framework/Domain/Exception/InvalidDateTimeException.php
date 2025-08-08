<?php

declare(strict_types=1);

namespace App\Framework\Domain\Exception;

use RuntimeException;

use function sprintf;

final class InvalidDateTimeException extends RuntimeException
{
    private function __construct(
        public string $template,
    ) {
        parent::__construct(sprintf($this->template, 'date time'));
    }

    public static function fromEmptyDateTime(): self
    {
        return new self('The %s value cannot be empty.');
    }

    public static function fromInvalidDateTime(): self
    {
        return new self('The %s value cannot be in an invalid format.');
    }
}

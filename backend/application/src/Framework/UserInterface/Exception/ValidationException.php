<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\Exception;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    /** @var array<string, string[]> */
    public readonly array $errors;

    /** @param array<string, string[]> $errors */
    private function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct('Validation failed');
    }

    /** @param array<string, string[]> $errors */
    public static function fromErrors(array $errors): self
    {
        return new self($errors);
    }
}

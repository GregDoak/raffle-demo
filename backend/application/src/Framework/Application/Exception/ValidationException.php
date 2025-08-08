<?php

declare(strict_types=1);

namespace App\Framework\Application\Exception;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    /** @var string[] */
    public readonly array $errors;

    /** @param string[] $errors */
    private function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct('Validation failed');
    }

    public static function fromError(string $error): self
    {
        return new self([$error]);
    }

    /** @param string[] $errors */
    public static function fromErrors(array $errors): self
    {
        return new self($errors);
    }
}

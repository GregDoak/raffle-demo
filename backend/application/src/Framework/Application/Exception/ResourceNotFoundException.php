<?php

declare(strict_types=1);

namespace App\Framework\Application\Exception;

use RuntimeException;

use function sprintf;

final class ResourceNotFoundException extends RuntimeException
{
    private function __construct(string $id)
    {
        parent::__construct(sprintf('The requested id "%s" was not found.', $id));
    }

    public static function fromId(string $id): self
    {
        return new self($id);
    }
}

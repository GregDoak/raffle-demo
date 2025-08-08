<?php

declare(strict_types=1);

namespace App\Framework\Domain\Exception;

use RuntimeException;

use function sprintf;

final class InvalidDomainNameException extends RuntimeException
{
    public static function fromEmptyName(string $className): self
    {
        return new self(sprintf('The aggregate name "%s" is required and cannot be empty.', $className));
    }
}

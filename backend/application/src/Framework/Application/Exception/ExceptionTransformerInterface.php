<?php

declare(strict_types=1);

namespace App\Framework\Application\Exception;

use Throwable;

interface ExceptionTransformerInterface
{
    public function transform(Throwable $exception): Throwable;
}

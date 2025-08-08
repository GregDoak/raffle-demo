<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem;

use App\Framework\UserInterface\ApiProblem\ProblemDetail\ProblemDetailInterface;
use Throwable;

interface ExceptionTransformerInterface
{
    public function transform(Throwable $exception): ProblemDetailInterface;
}

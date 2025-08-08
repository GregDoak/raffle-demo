<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem\ProblemDetail;

interface ProblemDetailInterface
{
    public function getStatus(): int;

    /** @return array<string, mixed> */
    public function toArray(): array;
}

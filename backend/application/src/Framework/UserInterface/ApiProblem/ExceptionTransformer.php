<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem;

use App\Framework\UserInterface\ApiProblem\ProblemDetail\ProblemDetailInterface;
use Throwable;

final readonly class ExceptionTransformer extends AbstractExceptionTransformer
{
    /** @param array<string, mixed> $additionalParams */
    public function __construct(
        InstanceProviderInterface $instanceProvider,
        private array $additionalParams = [],
    ) {
        parent::__construct($instanceProvider);
    }

    public function transform(Throwable $exception): ProblemDetailInterface
    {
        return $this->convertExceptionToProblemDetails($exception, $this->additionalParams);
    }
}

<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem;

use App\Framework\Application\Exception\ValidationException;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\InternalServerErrorProblemDetail;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\MethodNotAllowedProblemDetail;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\NotFoundProblemDetail;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\ProblemDetailInterface;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\ValidationProblemDetail;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

abstract readonly class AbstractExceptionTransformer implements ExceptionTransformerInterface
{
    private string $instance;

    public function __construct(
        InstanceProviderInterface $instanceProvider,
    ) {
        $this->instance = $instanceProvider->getInstance();
    }

    /** @param array<string, mixed> $additionalParams */
    protected function convertExceptionToProblemDetails(
        Throwable $exception,
        array $additionalParams = [],
    ): ProblemDetailInterface {
        return match ($exception::class) {
            ValidationException::class => new ValidationProblemDetail(
                $this->instance,
                array_merge(['errors' => $exception->errors], $additionalParams),
            ),
            NotFoundHttpException::class => new NotFoundProblemDetail($this->instance, $additionalParams),
            MethodNotAllowedHttpException::class => new MethodNotAllowedProblemDetail(
                $this->instance, $additionalParams,
            ),
            default => new InternalServerErrorProblemDetail($this->instance, $additionalParams),
        };
    }
}

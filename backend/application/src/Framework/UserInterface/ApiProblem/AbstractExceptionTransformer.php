<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem;

use App\Framework\Application\Exception\ValidationException as ApplicationValidationException;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\InternalServerErrorProblemDetail;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\MethodNotAllowedProblemDetail;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\NotFoundProblemDetail;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\ProblemDetailInterface;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\UnauthorizedProblemDetail;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\ValidationProblemDetail;
use App\Framework\UserInterface\Exception\ValidationException as UserInterfaceValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
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
    protected function convertExceptionToProblemDetail(
        Throwable $exception,
        array $additionalParams = [],
    ): ProblemDetailInterface {
        if ($exception::class === HttpException::class && $exception->getPrevious() !== null) {
            $exception = $exception->getPrevious();
        }

        return match ($exception::class) {
            ApplicationValidationException::class => new ValidationProblemDetail(
                $this->instance,
                array_merge(['errors' => $exception->errors], $additionalParams),
            ),
            UserInterfaceValidationException::class => new ValidationProblemDetail(
                $this->instance,
                array_merge(['errors' => $exception->errors], $additionalParams),
            ),
            InsufficientAuthenticationException::class => new UnauthorizedProblemDetail($this->instance, $additionalParams),
            NotFoundHttpException::class => new NotFoundProblemDetail($this->instance, $additionalParams),
            MethodNotAllowedHttpException::class => new MethodNotAllowedProblemDetail(
                $this->instance, $additionalParams,
            ),
            default => new InternalServerErrorProblemDetail($this->instance, $additionalParams),
        };
    }
}

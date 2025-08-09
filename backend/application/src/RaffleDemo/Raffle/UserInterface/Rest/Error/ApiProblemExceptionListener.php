<?php

declare(strict_types=1);

namespace App\RaffleDemo\Raffle\UserInterface\Rest\Error;

use App\Framework\UserInterface\ApiProblem\ExceptionTransformerInterface;
use App\Framework\UserInterface\ApiProblem\ProblemDetail\ProblemDetailInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
final readonly class ApiProblemExceptionListener
{
    public function __construct(
        private ExceptionTransformerInterface $exceptionTransformer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $exception = $event->getThrowable();
        if (str_starts_with($request->getPathInfo(), '/rest') === false) {
            return;
        }

        $problemDetail = $this->exceptionTransformer->transform($exception);
        $response = $this->generateResponse($problemDetail);

        if ($response->isServerError() === true) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        } else {
            $this->logger->info($exception->getMessage(), ['exception' => $exception]);
        }

        $event->setResponse($response);
    }

    private function generateResponse(ProblemDetailInterface $problemDetail): JsonResponse
    {
        return new JsonResponse(
            data: $problemDetail->toArray(),
            status: $problemDetail->getStatus(),
            headers: ['Content-Type' => 'application/problem+json'],
        );
    }
}

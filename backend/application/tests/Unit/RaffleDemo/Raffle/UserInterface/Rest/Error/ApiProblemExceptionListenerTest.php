<?php

declare(strict_types=1);

namespace App\Tests\Unit\RaffleDemo\Raffle\UserInterface\Rest\Error;

use App\Framework\UserInterface\ApiProblem\ExceptionTransformer;
use App\RaffleDemo\Raffle\UserInterface\Rest\Error\ApiProblemExceptionListener;
use App\Tests\Double\Framework\UserInterface\ApiProblem\DummyInstanceProvider;
use App\Tests\Double\RaffleDemo\Raffle\Infrastructure\Logger\InMemoryLogger;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ApiProblemExceptionListenerTest extends TestCase
{
    private ApiProblemExceptionListener $listener;
    private InMemoryLogger $logger;

    protected function setUp(): void
    {
        $this->logger = new InMemoryLogger();

        $this->listener = new ApiProblemExceptionListener(
            new ExceptionTransformer(new DummyInstanceProvider()),
            $this->logger,
        );
    }

    #[Test]
    public function it_only_handles_paths_beginning_with_rest(): void
    {
        // Arrange
        $event = new ExceptionEvent(
            self::createStub(HttpKernelInterface::class),
            Request::create('/not-rest'),
            HttpKernelInterface::MAIN_REQUEST,
            new Exception(),
        );

        // Act
        $this->listener->__invoke($event);

        // Assert
        self::assertEmpty($this->logger->getLogs());
    }

    #[Test]
    public function it_logs_a_critical_error_on_server_error(): void
    {
        // Arrange
        $exception = new Exception();
        $event = new ExceptionEvent(
            self::createStub(HttpKernelInterface::class),
            Request::create('/rest'),
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );

        // Act
        $this->listener->__invoke($event);

        // Assert
        $logs = $this->logger->getLogsForLevel('critical');
        self::assertCount(1, $logs);
        self::assertSame($exception, $logs[0]['context']['exception'] ?? null);
    }

    #[Test]
    public function it_logs_an_info_message_on_a_non_critical_error(): void
    {
        // Arrange
        $exception = new NotFoundHttpException();
        $event = new ExceptionEvent(
            self::createStub(HttpKernelInterface::class),
            Request::create('/rest'),
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );

        // Act
        $this->listener->__invoke($event);

        // Assert
        $logs = $this->logger->getLogsForLevel('info');
        self::assertCount(1, $logs);
        self::assertSame($exception, $logs[0]['context']['exception'] ?? null);
    }
}

<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Router;

use App\Framework\UserInterface\ApiProblem\InstanceProviderInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class SymfonyInstanceProvider implements InstanceProviderInterface
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public function getInstance(): string
    {
        return $this->router->getContext()->getPathInfo();
    }
}

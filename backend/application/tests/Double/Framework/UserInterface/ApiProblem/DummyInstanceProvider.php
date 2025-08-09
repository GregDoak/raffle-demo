<?php

declare(strict_types=1);

namespace App\Tests\Double\Framework\UserInterface\ApiProblem;

use App\Framework\UserInterface\ApiProblem\InstanceProviderInterface;

final readonly class DummyInstanceProvider implements InstanceProviderInterface
{
    public function getInstance(): string
    {
        return '/dummy';
    }
}

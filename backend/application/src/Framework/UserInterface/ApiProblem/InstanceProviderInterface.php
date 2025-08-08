<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem;

interface InstanceProviderInterface
{
    public function getInstance(): string;
}

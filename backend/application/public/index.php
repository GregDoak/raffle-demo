<?php

declare(strict_types=1);

use App\Framework\Kernel;

require_once \dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']); // @phpstan-ignore argument.type
};

<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require \dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) { // @phpstan-ignore function.alreadyNarrowedType
    (new Dotenv())->bootEnv(\dirname(__DIR__).'/.env');
}

if ((bool) $_SERVER['APP_DEBUG']) {
    umask(0000);
}

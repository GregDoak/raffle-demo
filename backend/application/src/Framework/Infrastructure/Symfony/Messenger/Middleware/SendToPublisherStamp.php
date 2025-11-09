<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger\Middleware;

use Symfony\Component\Messenger\Stamp\StampInterface;

final readonly class SendToPublisherStamp implements StampInterface
{
}

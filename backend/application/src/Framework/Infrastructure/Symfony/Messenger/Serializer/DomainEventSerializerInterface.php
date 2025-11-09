<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Messenger\Serializer;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

interface DomainEventSerializerInterface extends SerializerInterface
{
}

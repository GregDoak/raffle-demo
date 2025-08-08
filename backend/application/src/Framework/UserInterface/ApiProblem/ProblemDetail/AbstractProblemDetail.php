<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem\ProblemDetail;

abstract readonly class AbstractProblemDetail implements ProblemDetailInterface
{
    /** @var array<string, mixed> */
    private array $data;

    /** @param array<string, mixed> $additionalParams */
    public function __construct(
        string $type,
        private int $status,
        string $title,
        string $detail,
        string $instance,
        array $additionalParams = [],
    ) {
        $this->data = array_merge(
            [
                'type' => $type,
                'status' => $status,
                'title' => $title,
                'detail' => $detail,
                'instance' => $instance,
            ],
            $additionalParams,
        );
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->data;
    }
}

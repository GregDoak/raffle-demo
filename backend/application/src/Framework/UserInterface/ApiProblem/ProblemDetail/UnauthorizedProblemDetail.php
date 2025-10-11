<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem\ProblemDetail;

final readonly class UnauthorizedProblemDetail extends AbstractProblemDetail
{
    private const int STATUS = 401;
    private const string TYPE = 'https://www.rfc-editor.org/rfc/rfc9110.html#name-401-unauthorized';
    private const string TITLE = 'Unauthorized';

    public function __construct(string $instance, array $additionalParams = [])
    {
        parent::__construct(
            type: self::TYPE,
            status: self::STATUS,
            title: self::TITLE,
            detail: 'You are not authorized to access this page.',
            instance: $instance,
            additionalParams: $additionalParams,
        );
    }
}

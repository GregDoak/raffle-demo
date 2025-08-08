<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem\ProblemDetail;

final readonly class ValidationProblemDetail extends AbstractProblemDetail
{
    private const int STATUS = 400;
    private const string TYPE = 'https://www.rfc-editor.org/rfc/rfc9110.html#name-400-bad-request';

    private const string TITLE = 'Validation Error';

    public function __construct(string $instance, array $additionalParams = [])
    {
        parent::__construct(
            type: self::TYPE,
            status: self::STATUS,
            title: self::TITLE,
            detail: 'The request cannot be processed due to the following errors.',
            instance: $instance,
            additionalParams: $additionalParams,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem\ProblemDetail;

final readonly class InternalServerErrorProblemDetail extends AbstractProblemDetail
{
    private const int STATUS = 500;
    private const string TYPE = 'https://www.rfc-editor.org/rfc/rfc9110.html#name-500-internal-server-error';
    private const string TITLE = 'Internal Server Error';

    public function __construct(string $instance, array $additionalParams = [])
    {
        parent::__construct(
            type: self::TYPE,
            status: self::STATUS,
            title: self::TITLE,
            detail: 'An error has occurred while processing your request, please try again.',
            instance: $instance,
            additionalParams: $additionalParams,
        );
    }
}

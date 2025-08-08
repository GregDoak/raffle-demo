<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem\ProblemDetail;

final readonly class MethodNotAllowedProblemDetail extends AbstractProblemDetail
{
    private const int STATUS = 405;
    private const string TYPE = 'https://www.rfc-editor.org/rfc/rfc9110.html#name-405-method-not-allowed';
    private const string TITLE = 'Method Not Allowed';

    public function __construct(string $instance, array $additionalParams = [])
    {
        parent::__construct(
            type: self::TYPE,
            status: self::STATUS,
            title: self::TITLE,
            detail: 'This method is not allowed for this resource.',
            instance: $instance,
            additionalParams: $additionalParams,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Framework\UserInterface\ApiProblem\ProblemDetail;

final readonly class NotFoundProblemDetail extends AbstractProblemDetail
{
    private const int STATUS = 404;
    private const string TYPE = 'https://www.rfc-editor.org/rfc/rfc9110.html#name-404-not-found';

    private const string TITLE = 'Not Found';

    public function __construct(string $instance, array $additionalParams = [])
    {
        parent::__construct(
            type: self::TYPE,
            status: self::STATUS,
            title: self::TITLE,
            detail: 'The page you are looking for does not exist.',
            instance: $instance,
            additionalParams: $additionalParams,
        );
    }
}

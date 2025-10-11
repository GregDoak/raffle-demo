<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Security\Admin;

use Psr\Log\LoggerInterface;
use SensitiveParameter;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Throwable;

final readonly class AdminAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        try {
            $userIdentifier = (string) base64_decode($accessToken, true);

            return new UserBadge(
                $userIdentifier,
                static fn (string $userIdentifier) => new AdminUser($userIdentifier),
            );
        } catch (Throwable $exception) {
            $this->logger->notice('Authentication Failed', ['exception' => $exception]);

            throw new BadCredentialsException('Invalid credentials.');
        }
    }
}

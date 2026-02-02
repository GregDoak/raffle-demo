<?php

declare(strict_types=1);

namespace App\Framework\Infrastructure\Symfony\Security\Admin;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;

use function sprintf;

final readonly class AdminUser implements UserInterface
{
    public function __construct(
        private string $userIdentifier,
    ) {
        if ($this->userIdentifier === '' || filter_var($this->userIdentifier, FILTER_VALIDATE_EMAIL) === false) {
            throw new BadCredentialsException(sprintf('Invalid credentials "%s" is not a valid email address.', $this->userIdentifier));
        }
    }

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }
}

<?php

namespace App\Infrastructure\Shared\Security;

use App\Domain\User\Entity\User as AppUser;
use App\Domain\User\ValueObject\UserStatus;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!$user->isVerified) {
            throw new CustomUserMessageAccountStatusException(
                $this->translator->trans('account.not_verified', [], 'register')
            );
        }

        if (UserStatus::SUSPENDED === $user->status) {
            throw new CustomUserMessageAccountStatusException(
                $this->translator->trans('account.suspended', [], 'register')
            );
        }

    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }
}

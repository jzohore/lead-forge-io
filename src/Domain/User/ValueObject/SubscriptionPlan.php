<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

enum SubscriptionPlan: string
{
    case FREE = 'free';
    case STARTER = 'starter';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';

    public function getLabel(): string
    {
        return match ($this) {
            self::FREE => 'Gratuit',
            self::STARTER => 'Starter',
            self::PRO => 'Pro',
            self::ENTERPRISE => 'Enterprise',
        };
    }

    public function getMonthlyPrice(): int
    {
        return match ($this) {
            self::FREE => 0,
            self::STARTER => 29,
            self::PRO => 99,
            self::ENTERPRISE => 299,
        };
    }

    public function getMonthlyCredits(): int
    {
        return match ($this) {
            self::FREE => 100,
            self::STARTER => 1000,
            self::PRO => 5000,
            self::ENTERPRISE => 20000,
        };
    }

    public function getFeatures(): array
    {
        return match ($this) {
            self::FREE => [
                '100 crédits/mois',
                'Recherche basique',
                'API limitée',
            ],
            self::STARTER => [
                '1 000 crédits/mois',
                'Recherche avancée',
                'Vérification emails',
                'Export CSV',
            ],
            self::PRO => [
                '5 000 crédits/mois',
                'Toutes les fonctionnalités Starter',
                'API complète',
                'Webhook',
                'Support prioritaire',
            ],
            self::ENTERPRISE => [
                '20 000 crédits/mois',
                'Toutes les fonctionnalités Pro',
                'SLA garanti',
                'Support dédié',
                'Onboarding personnalisé',
            ],
        };
    }
}

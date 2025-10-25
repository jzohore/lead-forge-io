<?php

namespace App\Domain\User\ValueObject;

enum SubscriptionStatus: string
{
    case FREE = 'free';
    case ACTIVE = 'active';
    case CANCELED = 'canceled';
    case PAST_DUE = 'past_due';
    case UNPAID = 'unpaid';
    case TRIALING = 'trialing';

    public function isActive(): bool
    {
        return match ($this) {
            self::ACTIVE, self::TRIALING => true,
            default => false,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::FREE => 'Gratuit',
            self::ACTIVE => 'Actif',
            self::CANCELED => 'Annulé',
            self::PAST_DUE => 'Paiement en retard',
            self::UNPAID => 'Impayé',
            self::TRIALING => 'Période d\'essai',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'badge-success',
            self::TRIALING => 'badge-info',
            self::CANCELED => 'badge-warning',
            self::PAST_DUE, self::UNPAID => 'badge-danger',
            default => 'badge-secondary',
        };
    }
}

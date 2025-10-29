<?php

declare(strict_types=1);

namespace App\Domain\EarlyAccess\ValueObject;

enum UserSegment: string
{
    case ENTERPRISE = 'enterprise';      // Email pro requis
    case STARTUP = 'startup';      // Gmail OK si justifié
    case FREELANCE = 'freelance';     // Tout accepté

    public function label(): string
    {
        return match ($this) {
            self::ENTERPRISE => 'Une entreprise',
            self::STARTUP => 'Start-up',
            self::FREELANCE => 'Freelance',
        };
    }

    /**
     * Choix pour les formulaires Symfony.
     *
     * @return array<string, string> [label => value]
     */
    public static function choices(): array
    {
        return array_combine(
            array_map(fn (self $status) => $status->label(), self::cases()),
            array_map(fn (self $status) => $status->value, self::cases())
        );
    }
}

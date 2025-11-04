<?php

namespace App\Domain\SearchQuery\ValueObject;

/**
 * Enum pour représenter le type de lead d'une SearchQuery.
 */
enum LeadType: string
{
    case COMPANY = 'company';
    case PERSON = 'person';
    case EMAIL = 'email';

    /**
     * Retourne un label lisible pour l'UI ou les logs
     */
    public function label(): string
    {
        return match($this) {
            self::COMPANY => 'Entreprise',
            self::PERSON => 'Personne',
            self::EMAIL => 'Email',
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

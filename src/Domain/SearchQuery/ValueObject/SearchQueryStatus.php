<?php

declare(strict_types=1);

namespace App\Domain\SearchQuery\ValueObject;

enum SearchQueryStatus: string
{
    // Création / attente de traitement
    case PENDING = 'pending';
    case QUEUED = 'queued';
    case PROCESSING = 'processing';

    // Statuts liés au résultat
    case COMPLETED = 'completed';
    case NO_RESULT = 'no_result';
    case PARTIAL = 'partial';

    // Statuts d’erreur / retry
    case FAILED = 'failed';
    case RETRY = 'retry';
    case RATE_LIMITED = 'rate_limited';
    case TIMEOUT = 'timeout';

    // Statuts de vérification / scoring
    case VERIFIED = 'verified';
    case INVALID = 'invalid';
    case UNKNOWN = 'unknown';

    // Statuts RGPD / suppression
    case ANONYMIZED = 'anonymized';
    case DELETED = 'deleted';

    /**
     * Retourne un label humain lisible pour l'UI ou les logs.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::QUEUED => 'En file d’attente',
            self::PROCESSING => 'En cours de traitement',
            self::COMPLETED => 'Terminé',
            self::NO_RESULT => 'Aucun résultat',
            self::PARTIAL => 'Partiel',
            self::FAILED => 'Erreur',
            self::RETRY => 'Réessai',
            self::RATE_LIMITED => 'Limité par l’API',
            self::TIMEOUT => 'Timeout',
            self::VERIFIED => 'Vérifié',
            self::INVALID => 'Invalide',
            self::UNKNOWN => 'Inconnu',
            self::ANONYMIZED => 'Anonymisé',
            self::DELETED => 'Supprimé',
        };
    }
}

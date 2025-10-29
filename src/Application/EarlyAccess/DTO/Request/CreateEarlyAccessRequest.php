<?php

declare(strict_types=1);

namespace App\Application\EarlyAccess\DTO\Request;

use App\Domain\EarlyAccess\Entity\EarlyAccess;
use App\Domain\EarlyAccess\ValueObject\UserSegment;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: EarlyAccess::class)]
final class CreateEarlyAccessRequest
{
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(
        message: 'L\'email {{ value }} n\'est pas valide',
        mode: 'strict'
    )]
    #[Assert\Length(max: 180)]
    public string $email;

    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
    )]
    public ?string $lastName = null;

    public ?UserSegment $userSegment = null;

    /**
     * Normalise l'email en minuscules et retire les espaces.
     */
    public function getNormalizedEmail(): string
    {
        return strtolower(trim($this->email));
    }

    /**
     * Normalise le prénom (première lettre en majuscule).
     */
    public function getNormalizedFirstName(): ?string
    {
        return $this->firstName ? ucfirst(trim($this->firstName)) : null;
    }

    /**
     * Normalise le nom (première lettre en majuscule).
     */
    public function getNormalizedLastName(): ?string
    {
        return $this->lastName ? ucfirst(trim($this->lastName)) : null;
    }

    /**
     * Vérifie si l'email est professionnel (pas Gmail, Yahoo, etc.).
     */
    public function isProfessionalEmail(): bool
    {
        $personalDomains = [
            'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
            'live.com', 'icloud.com', 'protonmail.com', 'aol.com',
            'mail.com', 'yandex.com', 'gmx.com', 'yopmail.com',
        ];

        $domain = substr(strrchr($this->getNormalizedEmail(), '@'), 1);

        return ! in_array($domain, $personalDomains, true);
    }

    /**
     * Calcule un score de qualité (0-100).
     */
    public function calculateQualityScore(): int
    {
        $score = 50; // Base neutre

        // Email professionnel = +20
        if ($this->isProfessionalEmail()) {
            $score += 20;
        }

        // Nom + Prénom fournis = +15
        if ($this->firstName && $this->lastName) {
            $score += 15;
        }

        // Type utilisateur déclaré = +10
        if ($this->userSegment->name) {
            $score += 10;
        }

        // Domaine connu (ex: startup-tech.fr) = +5
        if ($this->isKnownDomain()) {
            $score += 5;
        }

        return min($score, 100);
    }

    /**
     * Détecte les domaines d'entreprises connues.
     */
    private function isKnownDomain(): bool
    {
        $knownDomains = [
            'salesforce.com', 'hubspot.com', 'microsoft.com',
            'google.com', 'meta.com', 'apple.com',
            // Tu peux enrichir avec une API Clearbit/Hunter
        ];

        return in_array($this->getEmailDomain(), $knownDomains, true);
    }

    public function getEmailDomain(): string
    {
        return substr(strrchr($this->email, '@'), 1);
    }

    /**
     * Recommandation d'accès basée sur le score.
     */
    public function getAccessRecommendation(): string
    {
        $score = $this->calculateQualityScore();

        return match (true) {
            $score >= 80 => 'immediate', // Accès direct
            $score >= 60 => 'onboarding', // Onboarding guidé
            $score >= 40 => 'limited',    // Limites renforcées
            default => 'manual_review'    // Review manuelle
        };
    }
}

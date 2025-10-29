<?php

declare(strict_types=1);

namespace App\Application\User\DTO\Request;

use App\Domain\User\Entity\User;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: User::class)]
final class CreateUserRequest
{
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(
        message: 'L\'email {{ value }} n\'est pas valide',
        mode: 'strict'
    )]
    #[Assert\Length(max: 180)]
    public string $email = '';

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire')]
    #[Assert\Length(
        min: 12,
        max: 4096,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le mot de passe est trop long'
    )]
    #[Assert\PasswordStrength(
        minScore: Assert\PasswordStrength::STRENGTH_MEDIUM,
        message: 'Le mot de passe est trop faible. Utilisez des majuscules, minuscules, chiffres et caractères spéciaux.'
    )]
    public string $plainPassword = '';

    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères'
    )]
    public string $firstName = '';

    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
    )]
    public string $lastName = '';

    #[Assert\Choice(
        choices: ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MODERATOR'],
        message: 'Le rôle {{ value }} n\'est pas valide'
    )]
    public array $roles = ['ROLE_USER'];

    public bool $isVerified = false;

    public bool $sendWelcomeEmail = true;

    /**
     * Email normalisé
     */
    public function getNormalizedEmail(): string
    {
        return strtolower(trim($this->email));
    }

    /**
     * Prénom normalisé
     */
    public function getNormalizedFirstName(): string
    {
        return ucfirst(strtolower(trim($this->firstName)));
    }

    /**
     * Nom normalisé
     */
    public function getNormalizedLastName(): string
    {
        return strtoupper(trim($this->lastName));
    }

    /**
     * Nom complet
     */
    public function getFullName(): string
    {
        return sprintf(
            '%s %s',
            $this->getNormalizedFirstName(),
            $this->getNormalizedLastName()
        );
    }

    /**
     * Vérifie si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->roles, true);
    }
}

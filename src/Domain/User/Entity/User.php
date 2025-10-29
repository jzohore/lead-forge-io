<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Profil\Entity\Profil;
use App\Infrastructure\User\Doctrine\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use function Symfony\Component\Clock\now;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email')]
final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use SoftDeleteableEntity;

    // ==================== IDENTIFICATION ====================

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public ?Uuid $id = null {
        get => $this->id;
    }

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide')]
    public ?string $email = null {
        get => $this->email;
        set => $this->email = strtolower(trim($value ?? ''));
    }

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Slug(fields: ['email'], unique: true)]
    public ?string $slug = null {
        get => $this->slug;
    }

    // ==================== AUTHENTIFICATION ====================

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: Types::JSON)]
    public array $roles = ['ROLE_USER'] {
        get => array_unique([...$this->roles, 'ROLE_USER']);
        set => $this->roles = array_values(array_unique($value));
    }

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $password = null {
        get => $this->password;
        set => $this->password = $value;
    }

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    public bool $isVerified = false {
        get => $this->isVerified;
        set => $this->isVerified = $value;
    }

    // ==================== TOKENS ====================

    #[ORM\Column(type: Types::STRING, length: 128, unique: true, nullable: true)]
    public ?string $validationToken = null {
        get => $this->validationToken;
        set => $this->validationToken = $value;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?\DateTimeImmutable $validationTokenExpiresAt = null {
        get => $this->validationTokenExpiresAt;
        set => $this->validationTokenExpiresAt = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 128, unique: true, nullable: true)]
    public ?string $resetToken = null {
        get => $this->resetToken;
        set => $this->resetToken = $value;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?\DateTimeImmutable $resetTokenExpiresAt = null {
        get => $this->resetTokenExpiresAt;
        set => $this->resetTokenExpiresAt = $value;
    }

    // ==================== PROFIL ====================

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    public ?string $firstName = null {
        get => $this->firstName;
        set => $this->firstName = $value ? ucfirst(trim($value)) : null;
    }

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    public ?string $lastName = null {
        get => $this->lastName;
        set => $this->lastName = $value ? ucfirst(trim($value)) : null;
    }

    // ==================== OAUTH ====================

    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: true)]
    public ?string $googleId = null {
        get => $this->googleId;
        set => $this->googleId = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    public ?string $hostedDomain = null {
        get => $this->hostedDomain;
        set => $this->hostedDomain = $value;
    }

    // ==================== TIMESTAMPS ====================

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    public \DateTimeImmutable $createdAt {
        get => $this->createdAt;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private \DateTimeImmutable $updatedAt {
        get => $this->updatedAt;
    }

    #[ORM\OneToOne(targetEntity: Profil::class, mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    public ?Profil $profile = null {
        get => $this->profile;
        set => $this->profile = $value;
    }

    public function __construct()
    {
        $this->createdAt = now()->setTimezone(new \DateTimeZone('Europe/Paris'));
        $this->updatedAt = now()->setTimezone(new \DateTimeZone('Europe/Paris'));
        //$this->generateValidationToken();
    }

    // ==================== MÉTHODES SECURITY ====================

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // Si vous stockez un plainPassword temporaire, nettoyez-le ici
    }

    // ==================== MÉTHODES MÉTIER ====================

    public function getFullName(): string
    {
        return trim(sprintf('%s %s', $this->firstName ?? '', $this->lastName ?? '')) ?: $this->email ?? 'Utilisateur';
    }

    public function isTokenValid(?string $token, ?\DateTimeImmutable $expiresAt): bool
    {
        if (null === $token || null === $expiresAt) {
            return false;
        }

        return $expiresAt > new \DateTimeImmutable();
    }

    public function isValidationTokenValid(): bool
    {
        return $this->isTokenValid($this->validationToken, $this->validationTokenExpiresAt);
    }

    public function isResetTokenValid(): bool
    {
        return $this->isTokenValid($this->resetToken, $this->resetTokenExpiresAt);
    }

    public function generateValidationToken(): void
    {
        $this->validationToken = bin2hex(random_bytes(64));
        $this->validationTokenExpiresAt = new \DateTimeImmutable('+24 hours');
    }

    public function generateResetToken(): void
    {
        $this->resetToken = bin2hex(random_bytes(64));
        $this->resetTokenExpiresAt = new \DateTimeImmutable('+1 hour');
    }

    public function clearValidationToken(): void
    {
        $this->validationToken = null;
        $this->validationTokenExpiresAt = null;
    }

    public function clearResetToken(): void
    {
        $this->resetToken = null;
        $this->resetTokenExpiresAt = null;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}

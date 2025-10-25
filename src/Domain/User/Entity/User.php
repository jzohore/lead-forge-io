<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\SubscriptionPlan;
use App\Domain\User\ValueObject\SubscriptionStatus;
use App\Infrastructure\User\Doctrine\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use libphonenumber\PhoneNumber;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
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
    private ?Uuid $id = null {
        get => $this->id;
    }

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide')]
    private ?string $email = null {
        get => $this->email;
        set => $this->email = strtolower(trim($value ?? ''));
    }

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Slug(fields: ['email'], unique: true)]
    private ?string $slug = null {
        get => $this->slug;
    }

    // ==================== AUTHENTIFICATION ====================

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = ['ROLE_USER'] {
        get => array_unique([...$this->roles, 'ROLE_USER']);
        set => $this->roles = array_values(array_unique($value));
    }

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $password = null {
        get => $this->password;
        set => $this->password = $value;
    }

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $isVerified = false {
        get => $this->isVerified;
        set => $this->isVerified = $value;
    }

    // ==================== TOKENS ====================

    #[ORM\Column(type: Types::STRING, length: 128, unique: true, nullable: true)]
    private ?string $validationToken = null {
        get => $this->validationToken;
        set => $this->validationToken = $value;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $validationTokenExpiresAt = null {
        get => $this->validationTokenExpiresAt;
        set => $this->validationTokenExpiresAt = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 128, unique: true, nullable: true)]
    private ?string $resetToken = null {
        get => $this->resetToken;
        set => $this->resetToken = $value;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $resetTokenExpiresAt = null {
        get => $this->resetTokenExpiresAt;
        set => $this->resetTokenExpiresAt = $value;
    }

    // ==================== PROFIL ====================

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    private ?string $firstName = null {
        get => $this->firstName;
        set => $this->firstName = $value ? ucfirst(trim($value)) : null;
    }

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    private ?string $lastName = null {
        get => $this->lastName;
        set => $this->lastName = $value ? ucfirst(trim($value)) : null;
    }

    // ==================== OAUTH ====================

    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: true)]
    private ?string $googleId = null {
        get => $this->googleId;
        set => $this->googleId = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $hostedDomain = null {
        get => $this->hostedDomain;
        set => $this->hostedDomain = $value;
    }

    // ==================== CRÉDITS ====================

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 100, 'unsigned' => true])]
    #[Assert\PositiveOrZero]
    private int $creditsRemaining = 100 {
        get => $this->creditsRemaining;
        set => $this->creditsRemaining = max(0, $value);
    }

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 100, 'unsigned' => true])]
    #[Assert\PositiveOrZero]
    private int $creditsTotal = 100 {
        get => $this->creditsTotal;
        set => $this->creditsTotal = max(0, $value);
    }

    // ==================== STRIPE / ABONNEMENT ====================

    #[ORM\Column(type: Types::STRING, length: 100, unique: true, nullable: true)]
    private ?string $stripeCustomerId = null {
        get => $this->stripeCustomerId;
        set => $this->stripeCustomerId = $value;
    }

    #[ORM\Column(
        type: Types::STRING,
        length: 50,
        nullable: true,
        enumType: SubscriptionStatus::class
    )]
    private ?SubscriptionStatus $subscriptionStatus = null {
        get => $this->subscriptionStatus;
        set => $this->subscriptionStatus = $value;
    }

    #[ORM\Column(
        type: Types::STRING,
        length: 50,
        nullable: true,
        enumType: SubscriptionPlan::class
    )]
    private ?SubscriptionPlan $subscriptionPlan = null {
        get => $this->subscriptionPlan;
        set => $this->subscriptionPlan = $value;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $subscriptionEndsAt = null {
        get => $this->subscriptionEndsAt;
        set => $this->subscriptionEndsAt = $value;
    }

    // ==================== TIMESTAMPS ====================

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTimeImmutable $createdAt {
        get => $this->createdAt;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private \DateTimeImmutable $updatedAt {
        get => $this->updatedAt;
    }

    #[ORM\Column(type: 'phone_number', nullable: true)]
    private ?PhoneNumber $phone {
        get => $this->phone;
        set => $this->phone = $value;
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

    public function hasActiveSubscription(): bool
    {
        return SubscriptionStatus::ACTIVE === $this->subscriptionStatus
            && (null === $this->subscriptionEndsAt || $this->subscriptionEndsAt > new \DateTimeImmutable());
    }

    public function hasCredits(): bool
    {
        return $this->creditsRemaining > 0;
    }

    public function consumeCredits(int $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Le montant doit être positif');
        }

        if ($this->creditsRemaining < $amount) {
            throw new \RuntimeException(sprintf('Crédits insuffisants (disponibles: %d, requis: %d)', $this->creditsRemaining, $amount));
        }

        $this->creditsRemaining -= $amount;
    }

    public function addCredits(int $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Le montant doit être positif');
        }

        $this->creditsRemaining += $amount;
        $this->creditsTotal += $amount;
    }

    public function resetCredits(int $amount = 100): void
    {
        $this->creditsRemaining = $amount;
        $this->creditsTotal = $amount;
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

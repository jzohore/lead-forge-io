<?php

declare(strict_types=1);

namespace App\Domain\Profil\Entity;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\SubscriptionPlan;
use App\Domain\User\ValueObject\SubscriptionStatus;
use App\Infrastructure\Profil\Doctrine\ProfilRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use libphonenumber\PhoneNumber;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
#[ORM\Table(name: 'user_profil')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
final class Profil
{
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null {
        get => $this->id;
    }

    // ==================== CRÉDITS ====================

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 100, 'unsigned' => true])]
    #[Assert\PositiveOrZero]
    private int $creditsRemaining = 100 {
        get => $this->creditsRemaining;
        set => $this->creditsRemaining = max(0, $value);
    }

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 100, 'unsigned' => true])]
    #[Assert\PositiveOrZero()]
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

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $subscriptionEndsAt = null {
        get => $this->subscriptionEndsAt;
        set => $this->subscriptionEndsAt = $value;
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

    #[ORM\Column(type: 'phone_number', nullable: true)]
    private ?PhoneNumber $phone {
        get => $this->phone;
        set => $this->phone = $value;
    }

    #[OneToOne(targetEntity: User::class, inversedBy: 'profil')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $user = null {
        get => $this->user;
        set => $this->user = $value;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTimeImmutable $createdAt {
        get => $this->createdAt;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
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
}

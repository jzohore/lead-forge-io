<?php

declare(strict_types=1);

namespace App\Domain\EarlyAccess\Entity;

use App\Domain\EarlyAccess\ValueObject\UserSegment;
use App\Infrastructure\EarlyAccess\Doctrine\EarlyAccessRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use function Symfony\Component\Clock\now;

#[ORM\Entity(repositoryClass: EarlyAccessRepository::class)]
#[ORM\Table(name: 'early_access')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
final class EarlyAccess
{
    use SoftDeleteableEntity;

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
        set => $this->slug = $value;
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

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    public ?string $lastName = null {
        get => $this->lastName;
        set => $this->lastName = $value ? ucfirst(trim($value)) : null;
    }

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, enumType: UserSegment::class)]
    public ?UserSegment $userSegment = null {
        get => $this->userSegment;
        set => $this->userSegment = $value;
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

    public function __construct()
    {
        $this->createdAt = now()->setTimezone(new \DateTimeZone('Europe/Paris'));
        $this->updatedAt = now()->setTimezone(new \DateTimeZone('Europe/Paris'));
        $this->generateValidationToken();
    }

    public function generateValidationToken(): void
    {
        $this->validationToken = bin2hex(random_bytes(64));
        $this->validationTokenExpiresAt = new \DateTimeImmutable('+24 hours');
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\SearchQuery\Entity;

use App\Domain\SearchQuery\ValueObject\CompanySizeLevel;
use App\Domain\SearchQuery\ValueObject\Industry;
use App\Domain\SearchQuery\ValueObject\LeadType;
use App\Domain\SearchQuery\ValueObject\SearchQueryStatus;
use App\Domain\User\Entity\User;
use App\Infrastructure\SearchQuery\Doctrine\SearchQueryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;

use function Symfony\Component\Clock\now;

use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SearchQueryRepository::class)]
#[ORM\Table(name: 'search_query')]
#[ORM\Index(columns: ['status'])]
#[ORM\Index(columns: ['created_at'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
final class SearchQuery
{
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public ?Uuid $id = null {
        get => $this->id;
    }

    #[ManyToOne(targetEntity: User::class, inversedBy: 'searchQuery')]
    #[JoinColumn(name: 'users', referencedColumnName: 'id', nullable: true,  onDelete: 'CASCADE')]
    public ?User $owner = null {
        get => $this->owner;
        set => $this->owner = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 255)]
    public string $query {
        get => $this->query;
        set => $this->query = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 50, enumType: LeadType::class)]
    public LeadType $leadType = LeadType::COMPANY {
        get => $this->leadType;
        set => $this->leadType = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 50, enumType: Industry::class)]
    public ?Industry $industry = Industry::SAAS {
        get => $this->industry;
        set => $this->industry = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 50, enumType: CompanySizeLevel::class)]
    public ?CompanySizeLevel $sizeLevel {
        get => $this->sizeLevel;
        set => $this->sizeLevel = $value;
    }

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 25])]
    public int $maxResults = 25 {
        get => $this->maxResults;
        set => $this->maxResults = $value;
    }

    #[ORM\Column(type: Types::JSON, nullable: true)]
    public ?array $parameters = null {
        get => $this->parameters;
        set => $this->parameters = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 50, enumType: SearchQueryStatus::class)]
    public SearchQueryStatus $status = SearchQueryStatus::PENDING {
        get => $this->status;
        set => $this->status = $value;
    }

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    public int $resultsCount = 0 {
        get => $this->resultsCount;
        set => $this->resultsCount = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 1024, nullable: true)]
    public ?string $error = null {
        get => $this->error;
        set => $this->error = $value;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    public \DateTimeImmutable $createdAt {
        get => $this->createdAt;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    public ?\DateTimeImmutable $updatedAt {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    #[ORM\Column(name: 'executed_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?\DateTimeImmutable $executedAt = null {
        get => $this->executedAt;
        set => $this->executedAt = $value;
    }

    public function __construct()
    {
        $this->createdAt = now()->setTimezone(new \DateTimeZone('Europe/Paris'));
        $this->updatedAt = now()->setTimezone(new \DateTimeZone('Europe/Paris'));
    }

    public function markInProcess(): void
    {
        $this->status = SearchQueryStatus::PROCESSING;
        $this->executedAt = now()->setTimezone(new \DateTimeZone('Europe/Paris'));
    }

    public function markCompleted(): void
    {
        $this->status = SearchQueryStatus::COMPLETED;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function markFailed(string $reason): void
    {
        $this->status = SearchQueryStatus::FAILED;
        $this->error = $reason;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function incrementResults(int $n = 1): void
    {
        $this->resultsCount += $n;
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Retourne le label lisible du statut
    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    public function getLeadLabel(): string
    {
        return $this->leadType->label();
    }

    public function getIndustryLabel(): string
    {
        return $this->industry->label();
    }

    public function getSizeLabel(): string
    {
        return $this->sizeLevel->label();
    }
}

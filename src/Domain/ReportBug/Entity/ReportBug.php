<?php

declare(strict_types=1);

namespace App\Domain\ReportBug\Entity;

use App\Domain\User\Entity\User;
use App\Infrastructure\ReportBug\Doctrine\ReportBugRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\Clock\now;

#[ORM\Entity(repositoryClass: ReportBugRepository::class)]
#[ORM\Table(name: 'report_bug')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
final class ReportBug
{
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public ?Uuid $id = null {
        get => $this->id;
    }

    #[ManyToOne(targetEntity: User::class, inversedBy: 'reportBugs')]
    #[JoinColumn(name: 'users', referencedColumnName: 'id', nullable: true,  onDelete: 'CASCADE')]
    public ?User $owner = null {
        get => $this->owner;
        set => $this->owner = $value;
    }

    #[ORM\Column(type: Types::TEXT, length: 2500)]
    public ?string $message = null {
        get => $this->message;
        set => $this->message = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    public ?string $filename = null {
        get => $this->filename;
        set => $this->filename = $value;
    }

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    public ?string $mimeType = null {
        get => $this->mimeType;
        set => $this->mimeType = $value;
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

    public function __construct()
    {
        $this->createdAt = now()->setTimezone(new \DateTimeZone('Europe/Paris'));
        $this->updatedAt = now()->setTimezone(new \DateTimeZone('Europe/Paris'));
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Company\Entity;

use App\Infrastructure\Company\Doctrine\CompanyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;

use function Symfony\Component\Clock\now;

use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: 'company')]
#[ORM\Index(name: 'idx_siren', columns: ['siren'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
final class Company
{
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public ?Uuid $id = null {
        get => $this->id;
    }

    #[ORM\Column(type: 'string', length: 255)]
    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $siren = null {
        get => $this->siren;
        set => $this->siren = $value;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $siret = null {
        get => $this->siret;
        set => $this->siret = $value;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $geoAdresse = null {
        get => $this->geoAdresse;
        set => $this->geoAdresse = $value;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $website = null {
        get => $this->website;
        set => $this->website = $value;
    }

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public ?\DateTimeImmutable $dateCreation = null {
        get => $this->dateCreation;
        set => $this->dateCreation = $value;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $sectionActivitePrincipale = null {
        get => $this->sectionActivitePrincipale;
        set => $this->sectionActivitePrincipale = $value;
    } // section macro secteur A,B,C,.

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

<?php

declare(strict_types=1);

namespace App\Application\Company\DTO\Response;

use App\Domain\Company\Entity\Company;

final readonly class CompanyCreatedResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $siren = null,
        public ?string $siret = null,
        public ?string $geoAdresse = null,
        public ?string $website = null,
        public ?string $sectionActivitePrincipale = null,
        public ?\DateTimeImmutable $dateCreation = null,
    ) {
    }

    public static function fromEntity(Company $company): self
    {
        return new self(
            id: $company->id->toRfc4122(),
            name: $company->name,
            siren: $company->siren,
            siret: $company->siret,
            geoAdresse: $company->geoAdresse,
            website: $company->website,
            sectionActivitePrincipale: $company->sectionActivitePrincipale,
            dateCreation: $company->dateCreation,
        );
    }
}

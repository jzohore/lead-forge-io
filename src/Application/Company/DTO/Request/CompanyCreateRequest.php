<?php

namespace App\Application\Company\DTO\Request;


use App\Domain\Company\Entity\Company;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: Company::class)]
class CompanyCreateRequest
{
    public ?string $name = null;

    #[Assert\Length(
        min: 9,
        max: 9,
        exactMessage: 'Le SIREN doit contenir exactement 9 chiffres'
    )]
    #[Assert\Regex('/^\d+$/', message: 'Le SIREN doit contenir uniquement des chiffres')]
    public ?string $siren = null;

    public ?string $siret = null;

    public ?string $geoAdresse = null;

    #[Assert\Url(message: 'Le site web doit être une URL valide')]
    public ?string $website = null;

    public ?string $sectionActivitePrincipale = null;


    public ?\DateTimeImmutable $dateCreation = null;
}

<?php

namespace App\Application\SearchQuery\DTO\Request;

use App\Domain\SearchQuery\Entity\SearchQuery;
use App\Domain\SearchQuery\ValueObject\CompanySizeLevel;
use App\Domain\SearchQuery\ValueObject\Industry;
use App\Domain\SearchQuery\ValueObject\LeadType;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: SearchQuery::class)]
class SearchQueryCreateRequest
{
    #[Assert\NotBlank]
    public string $query;

    #[Assert\Choice(callback: [LeadType::class, 'cases'])]
    public ?LeadType $leadType = LeadType::COMPANY;

    #[Assert\Positive]
    public ?int $maxResults = 5;

    #[Assert\Choice(callback: [Industry::class, 'cases'])]
    public ?Industry $industry = Industry::SAAS;

    #[Assert\Choice(callback: [CompanySizeLevel::class, 'cases'])]
    public ?CompanySizeLevel $sizeLevel = CompanySizeLevel::LEVEL_2;

    #[Assert\Uuid(
        message: 'L\'identifiant utilisateur doit être un UUID v4 valide',
        versions: [4]
    )]
    public ?Uuid $userId = null;
}

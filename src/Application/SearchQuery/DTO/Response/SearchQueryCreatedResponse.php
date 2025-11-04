<?php

declare(strict_types=1);

namespace App\Application\SearchQuery\DTO\Response;

use App\Domain\SearchQuery\Entity\SearchQuery;

final readonly class SearchQueryCreatedResponse
{
    public function __construct(
        public string $id,
        public string $query,
        public string $leadTypeLabel,
        public string $industryLabel,
        public string $sizeLevelLabel,
        public ?int $maxResults = null,
        public ?array $parameters = null,
        public string $statusLabel,
        public int $resultsCount,
        public ?string $error,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
        public ?\DateTimeImmutable $executedAt,
        public ?string $owner = null,
    ) {
    }

    public static function fromEntity(SearchQuery $query): self
    {
        return new self(
            id: $query->id->toRfc4122(),
            query: $query->query,
            leadTypeLabel: $query->getLeadLabel(),
            industryLabel: $query->getIndustryLabel(),
            sizeLevelLabel: $query->getSizeLabel(),
            maxResults: $query->maxResults,
            parameters: $query->parameters,
            statusLabel: $query->getStatusLabel(),
            resultsCount: $query->resultsCount,
            error: $query->error,
            createdAt: $query->createdAt,
            updatedAt: $query->updatedAt,
            executedAt: $query->executedAt,
            owner: $query->owner->id->toRfc4122(),
        );
    }
}

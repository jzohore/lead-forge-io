<?php

declare(strict_types=1);

namespace App\Domain\Company\Port\Out;

use App\Domain\Company\Entity\Company;
use App\Domain\SearchQuery\ValueObject\CompanySizeLevel;
use App\Domain\SearchQuery\ValueObject\Industry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;

interface CompanyRepositoryInterface
{
    public function getById(Uuid $id): ?Company;

    public function getByName(?string $name): ?Company;

    public function save(Company $entity): void;

    public function delete(Company $entity): void;

    public function search(?string $querySearch = null, ?array $status = [], ?bool $isDeleted = null): QueryBuilder;

    // --- Nouvelles méthodes ---

    /**
     * Récupère une entreprise par SIREN (unique).
     */
    public function getBySiren(string $siren): ?Company;

    /**
     * Récupère toutes les entreprises correspondant à un filtre minimal pour le pipeline.
     */
    public function findByFilters(
        ?Industry $industry = null,
        ?CompanySizeLevel $sizeLevel = null,
        ?string $region = null,
        ?string $city = null
    ): array;

    /**
     * Récupère les entreprises qui n'ont pas encore été enrichies (ex: website, dirigeants, CA).
     */
    public function findNotEnriched(int $limit = 100): array;
}

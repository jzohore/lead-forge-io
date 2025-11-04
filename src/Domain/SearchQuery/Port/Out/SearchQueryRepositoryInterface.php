<?php

declare(strict_types=1);

namespace App\Domain\SearchQuery\Port\Out;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;
use App\Domain\SearchQuery\Entity\SearchQuery;

interface SearchQueryRepositoryInterface
{
    public function getById(Uuid $id): ?SearchQuery;
    public function save(SearchQuery $entity): void;
    public function delete(SearchQuery $entity): void;
    public function findPending(): array;
    public function findQueued(): array;
    public function findByOwner(int $userId, ?array $status = null): array;
    public function search(?string $querySearch = null, ?array $status = [], ?bool $isDeleted = null): QueryBuilder;
}

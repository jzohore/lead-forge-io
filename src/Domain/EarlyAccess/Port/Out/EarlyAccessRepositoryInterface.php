<?php

declare(strict_types=1);

namespace App\Domain\EarlyAccess\Port\Out;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;
use App\Domain\EarlyAccess\Entity\EarlyAccess;

interface EarlyAccessRepositoryInterface
{
    /**
     * @param Uuid $id
     */
    public function getById(Uuid $id): ?EarlyAccess;

    /**
     * @param EarlyAccess $entity
     */
    public function save(EarlyAccess $entity): void;

    /**
     * @param EarlyAccess $entity
     */
    public function delete(EarlyAccess $entity): void;

    /**
     * @param string|null $querySearch
     * @param array|null $status
     * @param bool|null $isDeleted
     * @return QueryBuilder
     */
    public function search(?string $querySearch = null, ?array $status = [], ?bool $isDeleted = null): QueryBuilder;

    public function getEmail(string $email): ?EarlyAccess;
}

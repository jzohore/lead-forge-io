<?php

declare(strict_types=1);

namespace App\Domain\Profil\Port\Out;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;
use App\Domain\Profil\Entity\Profil;

interface ProfilRepositoryInterface
{
    /**
     * @param Uuid $id
     */
    public function getById(Uuid $id): ?Profil;

    /**
     * @param Profil $entity
     */
    public function save(Profil $entity): void;

    /**
     * @param Profil $entity
     */
    public function delete(Profil $entity): void;

    /**
     * @param string|null $querySearch
     * @param array|null $status
     * @param bool|null $isDeleted
     * @return QueryBuilder
     */
    public function search(?string $querySearch = null, ?array $status = [], ?bool $isDeleted = null): QueryBuilder;
}
<?php

declare(strict_types=1);

namespace App\Domain\ReportBug\Port\Out;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;
use App\Domain\ReportBug\Entity\ReportBug;

interface ReportBugRepositoryInterface
{
    /**
     * @param Uuid $id
     */
    public function getById(Uuid $id): ?ReportBug;

    /**
     * @param ReportBug $entity
     */
    public function save(ReportBug $entity): void;

    /**
     * @param ReportBug $entity
     */
    public function delete(ReportBug $entity): void;

    /**
     * @param string|null $querySearch
     * @param array|null $status
     * @param bool|null $isDeleted
     * @return QueryBuilder
     */
    public function search(?string $querySearch = null, ?array $status = [], ?bool $isDeleted = null): QueryBuilder;
}
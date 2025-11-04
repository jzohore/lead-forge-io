<?php

declare(strict_types=1);

namespace App\Infrastructure\Company\Doctrine;

use App\Domain\Company\Entity\Company;
use App\Domain\Company\Port\Out\CompanyRepositoryInterface;
use App\Domain\SearchQuery\ValueObject\CompanySizeLevel;
use App\Domain\SearchQuery\ValueObject\Industry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class CompanyRepository extends ServiceEntityRepository implements CompanyRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Company::class);
    }

    public function getById(Uuid $id): ?Company
    {
        return $this->getEntityManager()->find(Company::class, $id);
    }

    public function getBySiren(string $siren): ?Company
    {
        return $this->findOneBy(['siren' => $siren]);
    }

    public function save(Company $entity): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function delete(Company $entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    public function search(?string $querySearch = null, ?array $status = [], ?bool $isDeleted = null): QueryBuilder
    {
        $em = $this->getEntityManager();
        $alias = strtolower(new \ReflectionClass(Company::class)->getShortName());

        $qb = $em->createQueryBuilder()
            ->from(Company::class, $alias)
            ->select($alias)
            ->orderBy($alias.'.createdAt', 'DESC');

        if (null !== $isDeleted) {
            $qb->andWhere(
                $isDeleted
                    ? $qb->expr()->isNotNull($alias.'.deletedAt')
                    : $qb->expr()->isNull($alias.'.deletedAt')
            );
        }

        if (! empty($status)) {
            $qb->andWhere($alias.'.status IN (:status)')
                ->setParameter('status', array_map(static fn ($s) => (string) $s, $status));
        }

        if (! empty($querySearch)) {
            $search = '%'.trim(mb_strtolower($querySearch)).'%';
            $meta = $em->getClassMetadata(Company::class);
            $stringFields = [];
            foreach ($meta->getFieldNames() as $field) {
                $type = $meta->getTypeOfField($field);
                if (in_array($type, ['string', 'text'], true)) {
                    $stringFields[] = $field;
                }
            }

            if ($stringFields) {
                $orX = $qb->expr()->orX();
                foreach ($stringFields as $field) {
                    $orX->add($qb->expr()->like('LOWER('.$alias.'.'.$field.')', ':query'));
                }
                $qb->andWhere($orX)->setParameter('query', $search);
            }
        }

        return $qb;
    }

    public function findByFilters(
        ?Industry $industry = null,
        ?CompanySizeLevel $sizeLevel = null,
        ?string $region = null,
        ?string $city = null
    ): array {
        $qb = $this->createQueryBuilder('c');

        if ($industry) {
            $qb->andWhere('c.industry = :industry')
                ->setParameter('industry', $industry);
        }

        if ($sizeLevel) {
            $qb->andWhere('c.sizeLevel = :sizeLevel')
                ->setParameter('sizeLevel', $sizeLevel);
        }

        if ($region) {
            $qb->andWhere('c.region = :region')
                ->setParameter('region', $region);
        }

        if ($city) {
            $qb->andWhere('c.city = :city')
                ->setParameter('city', $city);
        }

        return $qb->getQuery()->getResult();
    }

    public function findNotEnriched(int $limit = 100): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.website IS NULL OR c.dirigeantNom IS NULL OR c.ca IS NULL')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getByName(?string $name): ?Company
    {
        return $this->findOneBy(['name' => $name]);
    }
}

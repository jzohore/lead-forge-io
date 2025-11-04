<?php

declare(strict_types=1);

namespace App\Infrastructure\SearchQuery\Doctrine;

use App\Domain\SearchQuery\Entity\SearchQuery;
use App\Domain\SearchQuery\Port\Out\SearchQueryRepositoryInterface;
use App\Domain\SearchQuery\ValueObject\SearchQueryStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class SearchQueryRepository extends ServiceEntityRepository implements SearchQueryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchQuery::class);
    }

    public function getById(Uuid $id): ?SearchQuery
    {
        return $this->getEntityManager()->find(SearchQuery::class, $id);
    }

    public function save(SearchQuery $entity): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function delete(SearchQuery $entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    public function findPending(): array
    {
        return $this->createQueryBuilder('sq')
            ->where('sq.status = :status')
            ->setParameter('status', SearchQueryStatus::PENDING->value)
            ->orderBy('sq.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findQueued(): array
    {
        return $this->createQueryBuilder('sq')
            ->where('sq.status = :status')
            ->setParameter('status', SearchQueryStatus::QUEUED->value)
            ->orderBy('sq.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByOwner(int $userId, ?array $status = null): array
    {
        $qb = $this->createQueryBuilder('sq')
            ->where('sq.owner = :owner')
            ->setParameter('owner', $userId)
            ->orderBy('sq.createdAt', 'DESC');

        if (! empty($status)) {
            $statusValues = array_map(fn ($s) => (string) $s, $status);
            $qb->andWhere('sq.status IN (:status)')->setParameter('status', $statusValues);
        }

        return $qb->getQuery()->getResult();
    }

    public function search(?string $querySearch = null, ?array $status = [], ?bool $isDeleted = null): QueryBuilder
    {
        $alias = 'sq';
        $qb = $this->createQueryBuilder($alias)
            ->orderBy($alias.'.createdAt', 'DESC');

        if (null !== $isDeleted) {
            $qb->andWhere($isDeleted
                ? $qb->expr()->isNotNull($alias.'.deletedAt')
                : $qb->expr()->isNull($alias.'.deletedAt'));
        }

        if (! empty($status)) {
            $qb->andWhere($alias.'.status IN (:status)')
                ->setParameter('status', array_map(fn ($s) => (string) $s, $status));
        }

        if (! empty($querySearch)) {
            $search = '%'.mb_strtolower(trim($querySearch)).'%';
            $meta = $this->getEntityManager()->getClassMetadata(SearchQuery::class);
            $stringFields = array_filter($meta->getFieldNames(), fn ($f) => in_array($meta->getTypeOfField($f), ['string', 'text'], true));

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
}

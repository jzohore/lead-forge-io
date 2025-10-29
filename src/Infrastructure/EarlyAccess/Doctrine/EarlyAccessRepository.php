<?php

declare(strict_types=1);

namespace App\Infrastructure\EarlyAccess\Doctrine;

use App\Domain\EarlyAccess\Entity\EarlyAccess;
use App\Domain\EarlyAccess\Port\Out\EarlyAccessRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class EarlyAccessRepository extends ServiceEntityRepository implements EarlyAccessRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, EarlyAccess::class);
    }

    public function getById(Uuid $id): ?EarlyAccess
    {
        return $this->getEntityManager()->find(EarlyAccess::class, $id);
    }

    public function save(EarlyAccess $entity): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function delete(EarlyAccess $entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    public function search(?string $querySearch = null, ?array $status = [], ?bool $isDeleted = null): QueryBuilder
    {
        $em = $this->getEntityManager();

        $alias = strtolower(new \ReflectionClass(EarlyAccess::class)->getShortName());

        $qb = $em->createQueryBuilder()
            ->from(EarlyAccess::class, $alias)
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

            $meta = $em->getClassMetadata(EarlyAccess::class);
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

    public function getEmail(string $email): ?EarlyAccess
    {
        return $this->findOneBy([
            'email' => $email,
        ]);
    }
}

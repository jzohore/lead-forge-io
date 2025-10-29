<?php

declare(strict_types=1);
namespace App\Infrastructure\Profil\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use App\Domain\Profil\Entity\Profil;
use App\Domain\Profil\Port\Out\ProfilRepositoryInterface as ProfilRepositoryInterface;

final class ProfilRepository extends ServiceEntityRepository implements ProfilRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Profil::class);
    }

    public function getById(Uuid $id): ?Profil
    {
        return $this->getEntityManager()->find(Profil::class, $id);
    }

    public function save(Profil $entity): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    public function delete(Profil $entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    public function search(?string $querySearch = null, ?array $status = [], ?bool $isDeleted = null): QueryBuilder
    {
        $em = $this->getEntityManager();

        $alias = strtolower((new \ReflectionClass(Profil::class))->getShortName());

        $qb = $em->createQueryBuilder()
            ->from(Profil::class, $alias)
            ->select($alias)
            ->orderBy($alias . '.createdAt', 'DESC');
            ;

        if ($isDeleted !== null) {
            $qb->andWhere(
                $isDeleted
                    ? $qb->expr()->isNotNull($alias . '.deletedAt')
                    : $qb->expr()->isNull($alias . '.deletedAt')
            );
        }

        if (!empty($status)) {
            $qb->andWhere($alias . '.status IN (:status)')
               ->setParameter('status', array_map(static fn($s) => (string) $s, $status));
        }

        if (!empty($querySearch)) {
            $search = '%' . trim(mb_strtolower($querySearch)) . '%';

            $meta = $em->getClassMetadata(Profil::class);
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
                    $orX->add($qb->expr()->like('LOWER(' . $alias . '.' . $field . ')', ':query'));
                }
                $qb->andWhere($orX)->setParameter('query', $search);
            }
        }

        return $qb;
    }
}
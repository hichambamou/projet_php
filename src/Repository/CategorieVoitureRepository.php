<?php

namespace App\Repository;

use App\Entity\CategorieVoiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategorieVoiture>
 *
 * @method CategorieVoiture|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieVoiture|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieVoiture[]    findAll()
 * @method CategorieVoiture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieVoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieVoiture::class);
    }

    public function save(CategorieVoiture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CategorieVoiture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithVoitureCount(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.voitures', 'v')
            ->addSelect('COUNT(v.id) as voitureCount')
            ->groupBy('c.id')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

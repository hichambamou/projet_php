<?php

namespace App\Repository;

use App\Entity\CategorieVoiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategorieVoiture>
 */
class CategorieVoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieVoiture::class);
    }

    /**
     * Find all categories with the count of voitures in each
     */
    public function findWithVoitureCount(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'COUNT(v.id) as voitureCount')
            ->leftJoin('c.voitures', 'v')
            ->groupBy('c.id')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find categories that have available voitures
     */
    public function findWithAvailableVoitures(): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.voitures', 'v')
            ->where('v.statut = :statut')
            ->setParameter('statut', 'disponible')
            ->groupBy('c.id')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

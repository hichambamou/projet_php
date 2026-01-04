<?php

namespace App\Repository;

use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voiture>
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    /**
     * Find available voitures with their categories
     * 
     * @param int $limit Maximum number of results
     * @return Voiture[] Returns an array of Voiture objects
     */
    public function findAvailableWithCategories(int $limit = 10): array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.categorie', 'c')
            ->addSelect('c')
            ->where('v.statut = :statut')
            ->setParameter('statut', 'disponible')
            ->orderBy('v.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find voitures by category
     * 
     * @param int $categorieId Category ID
     * @return Voiture[] Returns an array of Voiture objects
     */
    public function findByCategorie(int $categorieId): array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.categorie', 'c')
            ->addSelect('c')
            ->where('c.id = :categorieId')
            ->setParameter('categorieId', $categorieId)
            ->orderBy('v.marque', 'ASC')
            ->addOrderBy('v.modele', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search voitures by criteria
     * 
     * @param array $criteria Search criteria
     * @return Voiture[] Returns an array of Voiture objects
     */
    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('v')
            ->leftJoin('v.categorie', 'c')
            ->addSelect('c');

        if (isset($criteria['marque']) && !empty($criteria['marque'])) {
            $qb->andWhere('v.marque LIKE :marque')
                ->setParameter('marque', '%' . $criteria['marque'] . '%');
        }

        if (isset($criteria['modele']) && !empty($criteria['modele'])) {
            $qb->andWhere('v.modele LIKE :modele')
                ->setParameter('modele', '%' . $criteria['modele'] . '%');
        }

        if (isset($criteria['categorieId']) && !empty($criteria['categorieId'])) {
            $qb->andWhere('c.id = :categorieId')
                ->setParameter('categorieId', $criteria['categorieId']);
        }

        if (isset($criteria['statut']) && !empty($criteria['statut'])) {
            $qb->andWhere('v.statut = :statut')
                ->setParameter('statut', $criteria['statut']);
        }

        if (isset($criteria['prixMax']) && !empty($criteria['prixMax'])) {
            $qb->andWhere('v.prixParJour <= :prixMax')
                ->setParameter('prixMax', $criteria['prixMax']);
        }

        return $qb->orderBy('v.marque', 'ASC')
            ->addOrderBy('v.modele', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

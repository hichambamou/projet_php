<?php

namespace App\DataFixtures;

use App\Entity\CategorieVoiture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieVoitureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            [
                'nom' => 'Famille',
                'description' => 'Véhicules spacieux et confortables, idéaux pour les voyages en famille avec beaucoup d\'espace de rangement.'
            ],
            [
                'nom' => 'Confort',
                'description' => 'Voitures haut de gamme avec équipements premium pour un confort de conduite optimal.'
            ],
            [
                'nom' => 'Économique',
                'description' => 'Véhicules économiques à faible consommation, parfaits pour les petits budgets et déplacements urbains.'
            ],
            [
                'nom' => '4x4',
                'description' => 'Véhicules tout-terrain puissants, conçus pour les aventures hors route et les terrains difficiles.'
            ]
        ];

        foreach ($categories as $categorieData) {
            $categorie = new CategorieVoiture();
            $categorie->setNom($categorieData['nom']);
            $categorie->setDescription($categorieData['description']);
            
            $manager->persist($categorie);
        }

        $manager->flush();
    }
}

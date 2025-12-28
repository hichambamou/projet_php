<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'voiture')]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $marque;

    #[ORM\Column(type: 'string', length: 100)]
    private string $modele;

    #[ORM\Column(type: 'integer')]
    private int $annee;

    #[ORM\Column(type: 'float')]
    private float $prixParJour;

    #[ORM\Column(type: 'string', length: 50)]
    private string $statut;

    #[ORM\Column(type: 'integer')]
    private int $nombrePlaces;

    #[ORM\Column(type: 'string', length: 50)]
    private string $typeCarburant;

    #[ORM\Column(type: 'string', length: 255)]
    private string $photoPrincipale;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\OneToMany(mappedBy: 'voiture', targetEntity: Reservation::class)]
    private $reservations;

    #[ORM\OneToMany(mappedBy: 'voiture', targetEntity: PhotoVoiture::class, cascade: ['persist', 'remove'])]
    private $photos;
}

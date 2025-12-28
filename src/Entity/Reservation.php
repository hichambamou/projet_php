<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'reservation')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateFin;

    #[ORM\Column(type: 'float')]
    private float $montant;

    #[ORM\Column(type: 'string', length: 50)]
    private string $statut;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private Voiture $voiture;
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'client')]
class Client extends Utilisateur
{
    #[ORM\Column(type: 'string', length: 255)]
    private string $adresse;

    #[ORM\Column(type: 'string', length: 20)]
    private string $telephone;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Reservation::class)]
    private $reservations;
}

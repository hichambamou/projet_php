<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'utilisateur')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'role', type: 'string')]
#[ORM\DiscriminatorMap(['CLIENT' => Client::class, 'ADMIN' => Administrateur::class])]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    protected string $nom;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    protected string $email;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $motDePasse;

    // getters & setters
}

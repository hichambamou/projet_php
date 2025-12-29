<?php

namespace App\Entity;

use App\Repository\AdministrateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
#[ORM\Table(name: 'administrateur')]
class Administrateur extends Utilisateur
{
    public function __construct()
    {
        // Role is automatically set by Doctrine discriminator
    }
}

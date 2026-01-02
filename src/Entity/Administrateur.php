<?php

namespace App\Entity;

use App\Repository\AdministrateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
#[ORM\Table(name: 'administrateur')]
class Administrateur extends Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
}

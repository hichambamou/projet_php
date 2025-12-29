<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'utilisateur')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'role', type: 'string')]
#[ORM\DiscriminatorMap([
    'CLIENT' => Client::class,
    'ADMIN' => Administrateur::class
])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
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

    // ======================
    // GETTERS & SETTERS
    // ======================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    // UserInterface methods
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        // Get the discriminator value from Doctrine
        $discriminatorValue = $this instanceof \App\Entity\Administrateur ? 'ADMIN' : 'CLIENT';
        return [$discriminatorValue === 'ADMIN' ? 'ROLE_ADMIN' : 'ROLE_CLIENT'];
    }

    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }
}

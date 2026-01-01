<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: 'utilisateur')]
<<<<<<< HEAD
=======
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'role', type: 'string')]
#[ORM\DiscriminatorMap([
    'CLIENT' => Client::class,
    'ADMIN' => Administrateur::class
])]
#[UniqueEntity(
    fields: ['email'],
    message: 'Cette adresse email est déjà utilisée.'
)]
>>>>>>> 896dabf16dcb436a647a083fe5d04991f362a9d6
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(name: 'nom', type: 'string', length: 100)]
    protected string $nom;

    #[ORM\Column(name: 'email', type: 'string', length: 150, unique: true)]
    protected string $email;

    #[ORM\Column(name: 'mot_de_passe', type: 'string', length: 255)]
    protected string $motDePasse;

    #[ORM\Column(name: 'role', type: 'string', length: 10, columnDefinition: "ENUM('CLIENT', 'ADMIN') NOT NULL")]
    protected string $role;

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

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getRoles(): array
    {
        return [$this->role === 'ADMIN' ? 'ROLE_ADMIN' : 'ROLE_CLIENT'];
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

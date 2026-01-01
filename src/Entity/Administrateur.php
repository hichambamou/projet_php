<?php

namespace App\Entity;

use App\Repository\AdministrateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
#[ORM\Table(name: 'administrateur')]
class Administrateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Utilisateur $utilisateur = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        if ($utilisateur !== null && $utilisateur->getId() !== null) {
            $this->id = $utilisateur->getId();
        }
        return $this;
    }

    // Delegation methods for UserInterface compatibility
    public function getNom(): string
    {
        return $this->utilisateur?->getNom() ?? '';
    }

    public function setNom(string $nom): self
    {
        if ($this->utilisateur === null) {
            $this->utilisateur = new Utilisateur();
            $this->utilisateur->setRole('ADMIN');
        }
        $this->utilisateur->setNom($nom);
        return $this;
    }

    public function getEmail(): string
    {
        return $this->utilisateur?->getEmail() ?? '';
    }

    public function setEmail(string $email): self
    {
        if ($this->utilisateur === null) {
            $this->utilisateur = new Utilisateur();
            $this->utilisateur->setRole('ADMIN');
        }
        $this->utilisateur->setEmail($email);
        return $this;
    }

    public function getMotDePasse(): string
    {
        return $this->utilisateur?->getMotDePasse() ?? '';
    }

    public function setMotDePasse(string $motDePasse): self
    {
        if ($this->utilisateur === null) {
            $this->utilisateur = new Utilisateur();
            $this->utilisateur->setRole('ADMIN');
        }
        $this->utilisateur->setMotDePasse($motDePasse);
        return $this;
    }

    // UserInterface methods
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getRoles(): array
    {
        return $this->utilisateur?->getRoles() ?? ['ROLE_ADMIN'];
    }

    public function getPassword(): string
    {
        return $this->getMotDePasse();
    }

    public function eraseCredentials(): void
    {
        $this->utilisateur?->eraseCredentials();
    }
}

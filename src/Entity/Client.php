<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'client')]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(name: 'adresse', type: 'string', length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: 'telephone', type: 'string', length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Reservation::class)]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setClient($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getClient() === $this) {
                $reservation->setClient(null);
            }
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
            $this->utilisateur->setRole('CLIENT');
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
            $this->utilisateur->setRole('CLIENT');
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
            $this->utilisateur->setRole('CLIENT');
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
        return $this->utilisateur?->getRoles() ?? ['ROLE_CLIENT'];
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

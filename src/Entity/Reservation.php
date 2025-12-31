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

    #[ORM\Column(name: 'date_debut', type: 'date')]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(name: 'date_fin', type: 'date')]
    private \DateTimeInterface $dateFin;

    #[ORM\Column(name: 'montant', type: 'float')]
    private float $montant;

    #[ORM\Column(name: 'statut', type: 'string', length: 20, columnDefinition: "ENUM('en_attente', 'confirmee', 'annulee') DEFAULT 'en_attente'")]
    private string $statut = 'en_attente';

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'voiture_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Voiture $voiture = null;

    // ======================
    // GETTERS & SETTERS
    // ======================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): \DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): \DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): self
    {
        $this->voiture = $voiture;
        return $this;
    }
}

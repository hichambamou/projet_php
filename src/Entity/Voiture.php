<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'voiture')]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'marque', type: 'string', length: 100)]
    private string $marque;

    #[ORM\Column(name: 'modele', type: 'string', length: 100)]
    private string $modele;

    #[ORM\Column(name: 'annee', type: 'integer')]
    private int $annee;

    #[ORM\Column(name: 'prix_par_jour', type: 'float')]
    private float $prixParJour;

    #[ORM\Column(name: 'statut', type: 'string', length: 20, columnDefinition: "ENUM('disponible', 'louee', 'maintenance') DEFAULT 'disponible'")]
    private string $statut = 'disponible';

    #[ORM\Column(name: 'nombre_places', type: 'integer', nullable: true)]
    private ?int $nombrePlaces = null;

    #[ORM\Column(name: 'type_carburant', type: 'string', length: 50, nullable: true)]
    private ?string $typeCarburant = null;

    #[ORM\Column(name: 'photo_principale', type: 'string', length: 255, nullable: true)]
    private ?string $photoPrincipale = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'voiture', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'voiture', targetEntity: PhotoVoiture::class, cascade: ['persist', 'remove'])]
    private Collection $photos;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): self
    {
        $this->marque = $marque;
        return $this;
    }

    public function getModele(): string
    {
        return $this->modele;
    }

    public function setModele(string $modele): self
    {
        $this->modele = $modele;
        return $this;
    }

    public function getAnnee(): int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;
        return $this;
    }

    public function getPrixParJour(): float
    {
        return $this->prixParJour;
    }

    public function setPrixParJour(float $prixParJour): self
    {
        $this->prixParJour = $prixParJour;
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

    public function getNombrePlaces(): ?int
    {
        return $this->nombrePlaces;
    }

    public function setNombrePlaces(?int $nombrePlaces): self
    {
        $this->nombrePlaces = $nombrePlaces;
        return $this;
    }

    public function getTypeCarburant(): ?string
    {
        return $this->typeCarburant;
    }

    public function setTypeCarburant(?string $typeCarburant): self
    {
        $this->typeCarburant = $typeCarburant;
        return $this;
    }

    public function getPhotoPrincipale(): ?string
    {
        return $this->photoPrincipale;
    }

    public function setPhotoPrincipale(?string $photoPrincipale): self
    {
        $this->photoPrincipale = $photoPrincipale;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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
            $reservation->setVoiture($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getVoiture() === $this) {
                $reservation->setVoiture(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PhotoVoiture>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(PhotoVoiture $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setVoiture($this);
        }

        return $this;
    }

    public function removePhoto(PhotoVoiture $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            if ($photo->getVoiture() === $this) {
                $photo->setVoiture(null);
            }
        }

        return $this;
    }
}

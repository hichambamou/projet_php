<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CategorieVoiture;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'voiture')]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'marque', type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'La marque est obligatoire')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'La marque doit faire au moins {{ limit }} caractères', maxMessage: 'La marque ne peut pas dépasser {{ limit }} caractères')]
    private string $marque;

    #[ORM\Column(name: 'modele', type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Le modèle est obligatoire')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Le modèle doit faire au moins {{ limit }} caractères', maxMessage: 'Le modèle ne peut pas dépasser {{ limit }} caractères')]
    private string $modele;

    #[ORM\Column(name: 'annee', type: 'integer')]
    #[Assert\NotBlank(message: 'L\'année est obligatoire')]
    #[Assert\Range(min: 1900, max: 2100, notInRangeMessage: 'L\'année doit être entre {{ min }} et {{ max }}')]
    private int $annee;

    #[ORM\Column(name: 'prix_par_jour', type: 'float')]
    #[Assert\NotBlank(message: 'Le prix par jour est obligatoire')]
    #[Assert\Positive(message: 'Le prix par jour doit être positif')]
    #[Assert\LessThan(value: 10000, message: 'Le prix par jour ne peut pas dépasser {{ value }} DH')]
    private float $prixParJour;

    #[ORM\Column(name: 'statut', type: 'string', length: 20)]
    private string $statut = 'disponible';

    #[ORM\Column(name: 'nombre_places', type: 'integer', nullable: true)]
    private ?int $nombrePlaces = null;

    #[ORM\Column(name: 'type_carburant', type: 'string', length: 50, nullable: true)]
    private ?string $typeCarburant = null;

    #[ORM\Column(name: 'photo_principale', type: 'string', length: 255, nullable: true)]
    private ?string $photoPrincipale = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: CategorieVoiture::class, inversedBy: 'voitures')]
    #[ORM\JoinColumn(name: 'categorie_id', referencedColumnName: 'id', nullable: true)]
    private ?CategorieVoiture $categorie = null;

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

    public function getCategorie(): ?CategorieVoiture
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieVoiture $categorie): self
    {
        $this->categorie = $categorie;
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

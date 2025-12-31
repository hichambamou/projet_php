<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'photo_voiture')]
class PhotoVoiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'url', type: 'string', length: 255)]
    private string $url;

    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: 'photos')]
    #[ORM\JoinColumn(name: 'voiture_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Voiture $voiture = null;

    // ======================
    // GETTERS & SETTERS
    // ======================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
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

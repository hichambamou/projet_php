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

    #[ORM\Column(type: 'string', length: 255)]
    private string $url;

    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private Voiture $voiture;
}

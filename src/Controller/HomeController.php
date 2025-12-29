<?php

namespace App\Controller;

use App\Entity\Voiture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {
        $voitures = $em->getRepository(Voiture::class)->findBy(
            ['statut' => 'disponible'],
            ['id' => 'DESC'],
            6
        );

        return $this->render('home/index.html.twig', [
            'voitures' => $voitures,
        ]);
    }
}

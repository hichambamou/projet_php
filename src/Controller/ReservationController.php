<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Client;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $items = $em->getRepository(Reservation::class)->findAll();
        } else {
            $items = $em->getRepository(Reservation::class)->findBy(['client' => $user]);
        }
        return $this->render('reservation/index.html.twig', ['reservations' => $items]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    #[Route('/new/{voitureId}', name: 'app_reservation_new_voiture', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, ?int $voitureId = null): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $reservation = new Reservation();

        // Set client if not admin
        if ($user instanceof Client) {
            $reservation->setClient($user);
        }

        // Pre-select voiture if provided
        if ($voitureId) {
            $voiture = $em->getRepository(\App\Entity\Voiture::class)->find($voitureId);
            if ($voiture) {
                $reservation->setVoiture($voiture);
            }
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Always calculate amount automatically
            if ($reservation->getDateDebut() && $reservation->getDateFin() && $reservation->getVoiture()) {
                $days = $reservation->getDateDebut()->diff($reservation->getDateFin())->days + 1;
                $montant = $days * $reservation->getVoiture()->getPrixParJour();
                $reservation->setMontant($montant);
            }

            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Réservation créée avec succès!');
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', ['reservation' => $reservation, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', ['reservation' => $reservation]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Recalculate amount automatically
            if ($reservation->getDateDebut() && $reservation->getDateFin() && $reservation->getVoiture()) {
                $days = $reservation->getDateDebut()->diff($reservation->getDateFin())->days + 1;
                $montant = $days * $reservation->getVoiture()->getPrixParJour();
                $reservation->setMontant($montant);
            }

            $em->flush();
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', ['reservation' => $reservation, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $em->remove($reservation);
            $em->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}

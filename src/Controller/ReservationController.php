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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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
        // Bloquer l'édition si la réservation est confirmée
        if ($reservation->getStatut() === 'confirmee') {
            $this->addFlash('error', 'Impossible de modifier une réservation confirmée. Vous pouvez uniquement l\'annuler.');
            return $this->redirectToRoute('app_reservation_show', ['id' => $reservation->getId()]);
        }

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

    #[Route('/{id}/confirm', name: 'app_reservation_confirm', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function confirm(Request $request, Reservation $reservation, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if ($this->isCsrfTokenValid('confirm' . $reservation->getId(), $request->request->get('_token'))) {
            $reservation->setStatut('confirmee');
            $em->flush();

            // Envoyer l'email de confirmation
            try {
                $email = (new Email())
                    ->from('noreply@marokicars.com')
                    ->to($reservation->getClient()->getEmail())
                    ->subject('Confirmation de votre réservation #' . $reservation->getId())
                    ->html($this->renderView('emails/reservation_confirmation.html.twig', [
                        'reservation' => $reservation
                    ]));

                $mailer->send($email);
                $this->addFlash('success', 'Réservation confirmée avec succès! Un email de confirmation a été envoyé au client.');
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Réservation confirmée, mais l\'email n\'a pas pu être envoyé.');
            }
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/cancel', name: 'app_reservation_cancel', methods: ['POST'])]
    public function cancel(Request $request, Reservation $reservation, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if ($this->isCsrfTokenValid('cancel' . $reservation->getId(), $request->request->get('_token'))) {
            $previousStatut = $reservation->getStatut();
            $reservation->setStatut('annulee');
            $em->flush();

            // Envoyer un email d'annulation si la réservation était confirmée
            if ($previousStatut === 'confirmee') {
                try {
                    $email = (new Email())
                        ->from('noreply@marokicars.com')
                        ->to($reservation->getClient()->getEmail())
                        ->subject('Annulation de votre réservation #' . $reservation->getId())
                        ->html($this->renderView('emails/reservation_cancellation.html.twig', [
                            'reservation' => $reservation
                        ]));

                    $mailer->send($email);
                } catch (\Exception $e) {
                    // Silently fail email sending
                }
            }

            $this->addFlash('success', 'Réservation annulée avec succès!');
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
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

<?php

namespace App\Controller;

use App\Entity\PhotoVoiture;
use App\Form\PhotoVoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/photo_voiture')]
final class PhotoVoitureController extends AbstractController
{
    #[Route(name: 'app_photo_voiture_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $photos = $em->getRepository(PhotoVoiture::class)->findAll();
        return $this->render('photo_voiture/index.html.twig', ['photos' => $photos]);
    }

    #[Route('/new', name: 'app_photo_voiture_new', methods: ['GET','POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $photo = new PhotoVoiture();
        $form = $this->createForm(PhotoVoitureType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($photo);
            $em->flush();
            return $this->redirectToRoute('app_photo_voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photo_voiture/new.html.twig', ['photo' => $photo, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_photo_voiture_show', methods: ['GET'])]
    public function show(PhotoVoiture $photo): Response
    {
        return $this->render('photo_voiture/show.html.twig', ['photo' => $photo]);
    }

    #[Route('/{id}/edit', name: 'app_photo_voiture_edit', methods: ['GET','POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, PhotoVoiture $photo, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PhotoVoitureType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_photo_voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photo_voiture/edit.html.twig', ['photo' => $photo, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_photo_voiture_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, PhotoVoiture $photo, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->request->get('_token'))) {
            $em->remove($photo);
            $em->flush();
        }

        return $this->redirectToRoute('app_photo_voiture_index', [], Response::HTTP_SEE_OTHER);
    }
}

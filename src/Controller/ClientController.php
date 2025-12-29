<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_ADMIN')]
final class ClientController extends AbstractController
{
    #[Route(name: 'app_client_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $items = $em->getRepository(Client::class)->findAll();
        return $this->render('client/index.html.twig', ['clients' => $items]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($client, $client->getMotDePasse());
            $client->setMotDePasse($hashedPassword);
            $em->persist($client);
            $em->flush();
            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', ['client' => $client, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', ['client' => $client]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('motDePasse')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($client, $form->get('motDePasse')->getData());
                $client->setMotDePasse($hashedPassword);
            }
            $em->flush();
            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/edit.html.twig', ['client' => $client, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $em->remove($client);
            $em->flush();
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Client;
use App\Entity\Administrateur;
use App\Form\ClientType;
use App\Form\AdministrateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/users', name: 'app_admin_users', methods: ['GET'])]
    public function users(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(Utilisateur::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/new/client', name: 'app_admin_user_new_client', methods: ['GET', 'POST'])]
    public function newClient(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($client, $client->getMotDePasse());
            $client->setMotDePasse($hashedPassword);

            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/new_client.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/users/new/admin', name: 'app_admin_user_new_admin', methods: ['GET', 'POST'])]
    public function newAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $admin = new Administrateur();
        $form = $this->createForm(AdministrateurType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($admin, $admin->getMotDePasse());
            $admin->setMotDePasse($hashedPassword);

            $entityManager->persist($admin);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/new_admin.html.twig', [
            'administrateur' => $admin,
            'form' => $form,
        ]);
    }

    #[Route('/users/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function editUser(Request $request, Utilisateur $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($user instanceof Client) {
            $form = $this->createForm(ClientType::class, $user);
        } elseif ($user instanceof Administrateur) {
            $form = $this->createForm(AdministrateurType::class, $user);
        } else {
            throw $this->createNotFoundException('User type not supported');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If password is being changed, hash it
            if ($form->has('motDePasse') && $form->get('motDePasse')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $form->get('motDePasse')->getData());
                $user->setMotDePasse($hashedPassword);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/edit_user.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/users/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, Utilisateur $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
    }
}
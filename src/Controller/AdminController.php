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
            
            // Ensure utilisateur is created
            $utilisateur = $client->getUtilisateur();
            if ($utilisateur === null) {
                $utilisateur = new Utilisateur();
                $utilisateur->setNom($client->getNom());
                $utilisateur->setEmail($client->getEmail());
                $utilisateur->setMotDePasse($hashedPassword);
                $utilisateur->setRole('CLIENT');
                $client->setUtilisateur($utilisateur);
            }
            
            $entityManager->persist($utilisateur);
            $entityManager->flush(); // Flush to get the ID
            
            $client->setId($utilisateur->getId());
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
            
            // Ensure utilisateur is created
            $utilisateur = $admin->getUtilisateur();
            if ($utilisateur === null) {
                $utilisateur = new Utilisateur();
                $utilisateur->setNom($admin->getNom());
                $utilisateur->setEmail($admin->getEmail());
                $utilisateur->setMotDePasse($hashedPassword);
                $utilisateur->setRole('ADMIN');
                $admin->setUtilisateur($utilisateur);
            }
            
            $entityManager->persist($utilisateur);
            $entityManager->flush(); // Flush to get the ID
            
            $admin->setId($utilisateur->getId());
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
        // Find the corresponding Client or Administrateur based on role
        $entity = null;
        $form = null;
        
        if ($user->getRole() === 'CLIENT') {
            $entity = $entityManager->getRepository(Client::class)->find($user->getId());
            if ($entity === null) {
                // Create client if it doesn't exist
                $entity = new Client();
                $entity->setId($user->getId());
                $entity->setUtilisateur($user);
            }
            $form = $this->createForm(ClientType::class, $entity);
        } elseif ($user->getRole() === 'ADMIN') {
            $entity = $entityManager->getRepository(Administrateur::class)->find($user->getId());
            if ($entity === null) {
                // Create admin if it doesn't exist
                $entity = new Administrateur();
                $entity->setId($user->getId());
                $entity->setUtilisateur($user);
            }
            $form = $this->createForm(AdministrateurType::class, $entity);
        } else {
            throw $this->createNotFoundException('User type not supported');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Update utilisateur fields
            $user->setNom($entity->getNom());
            $user->setEmail($entity->getEmail());
            
            // If password is being changed, hash it
            if ($form->has('motDePasse') && $form->get('motDePasse')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($entity, $form->get('motDePasse')->getData());
                $user->setMotDePasse($hashedPassword);
                $entity->setMotDePasse($hashedPassword);
            }

            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/edit_user.html.twig', [
            'user' => $user,
            'entity' => $entity,
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
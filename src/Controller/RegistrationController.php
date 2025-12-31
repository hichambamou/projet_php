<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\UserAuthenticator;

#[Route('/register')]
class RegistrationController extends AbstractController
{
    #[Route('', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $client = new Client();
        $form = $this->createForm(RegistrationType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword(
                $client,
                $form->get('motDePasse')->getData()
            );
            $client->setMotDePasse($hashedPassword);
            // Role is automatically set by Doctrine discriminator (CLIENT)

            try {
                $entityManager->persist($client);
                $entityManager->flush();

                // Auto-login after registration
                return $userAuthenticator->authenticateUser(
                    $client,
                    $authenticator,
                    $request
                );
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Cette adresse email est déjà utilisée. Veuillez en choisir une autre.');
            }
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form,
        ]);
    }
}


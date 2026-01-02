<?php

namespace App\Command;

use App\Entity\Administrateur;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create or update an admin user with a hashed password',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email address')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin password (will be hashed)')
            ->addArgument('name', InputArgument::OPTIONAL, 'Admin name', 'Admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $name = $input->getArgument('name');

        // Check if admin already exists
        $repository = $this->entityManager->getRepository(Utilisateur::class);
        $existingAdmin = $repository->findOneBy(['email' => $email]);

        if ($existingAdmin && $existingAdmin instanceof Administrateur) {
            // Update existing admin's password
            $existingAdmin->setMotDePasse($hashedPassword);
            $existingAdmin->setNom($name);

            $this->entityManager->flush();

            $io->success(sprintf('Admin "%s" password has been updated!', $email));
        } else {
            // Create new admin
            $admin = new Administrateur();
            $admin->setNom($name);
            $admin->setEmail($email);
            
            $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
            $admin->setMotDePasse($hashedPassword);
            
            $this->entityManager->persist($admin);
            $this->entityManager->flush();

            $io->success(sprintf('Admin "%s" has been created with ID %d!', $email, $admin->getId()));
        }

        return Command::SUCCESS;
    }
}

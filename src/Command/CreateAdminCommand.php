<?php

namespace App\Command;

use App\Entity\Administrateur;
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
        $plainPassword = $input->getArgument('password');
        $name = $input->getArgument('name');

        $repository = $this->entityManager->getRepository(Administrateur::class);
        $existing = $repository->findOneBy(['email' => $email]);

        if ($existing) {
            $io->error(sprintf('Admin with email "%s" already exists.', $email));
            return Command::FAILURE;
        }

        $admin = new Administrateur();
        $admin->setNom($nom);
        $admin->setEmail($email);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, $plainPassword);
        $admin->setMotDePasse($hashedPassword);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success(sprintf('Admin user "%s" created successfully with email "%s".', $name, $email));

        return Command::SUCCESS;
    }
}

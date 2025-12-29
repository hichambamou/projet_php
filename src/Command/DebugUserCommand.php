<?php

namespace App\Command;

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
    name: 'app:debug-user',
    description: 'Debug user authentication and password hashing',
)]
class DebugUserCommand extends Command
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
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password to test (optional)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $testPassword = $input->getArgument('password');

        $repository = $this->entityManager->getRepository(Utilisateur::class);
        $user = $repository->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error(sprintf('User with email "%s" not found.', $email));
            return Command::FAILURE;
        }

        $io->success(sprintf('User found: %s', $user->getNom()));
        $io->table(
            ['Property', 'Value'],
            [
                ['ID', $user->getId()],
                ['Name', $user->getNom()],
                ['Email', $user->getEmail()],
                ['Class', get_class($user)],
                ['Roles', implode(', ', $user->getRoles())],
                ['Password Hash', substr($user->getPassword(), 0, 60) . '...'],
            ]
        );

        if ($testPassword) {
            $isValid = $this->passwordHasher->isPasswordValid($user, $testPassword);
            if ($isValid) {
                $io->success(sprintf('Password "%s" is VALID for this user.', $testPassword));
            } else {
                $io->error(sprintf('Password "%s" is INVALID for this user.', $testPassword));
                $io->note('The password in the database might be stored incorrectly.');
            }
        }

        return Command::SUCCESS;
    }
}

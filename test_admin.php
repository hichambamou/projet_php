<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin;

$kernel = new Kernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get(EntityManagerInterface::class);

try {
    // Tester le chargement de l'admin
    $admin = $entityManager->getRepository(Admin::class)->findOneBy(['email' => 'admin@maroki-cars.com']);

    if ($admin) {
        echo "Admin trouvé:\n";
        echo "ID: " . $admin->getId() . "\n";
        echo "Email: " . $admin->getEmail() . "\n";
        echo "Rôles: " . json_encode($admin->getRoles()) . "\n";
        echo "Mot de passe hashé: " . substr($admin->getPassword(), 0, 20) . "...\n";
    } else {
        echo "Admin non trouvé\n";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
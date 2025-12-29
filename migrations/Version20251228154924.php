<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251228154924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Create tables matching the exact database schema with ENUM types
        $this->addSql("CREATE TABLE utilisateur (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            mot_de_passe VARCHAR(255) NOT NULL,
            role ENUM('CLIENT', 'ADMIN') NOT NULL
        ) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $this->addSql("CREATE TABLE client (
            id INT PRIMARY KEY,
            adresse VARCHAR(255),
            telephone VARCHAR(20),
            CONSTRAINT fk_client_user
                FOREIGN KEY (id) REFERENCES utilisateur(id)
                ON DELETE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $this->addSql("CREATE TABLE administrateur (
            id INT PRIMARY KEY,
            CONSTRAINT fk_admin_user
                FOREIGN KEY (id) REFERENCES utilisateur(id)
                ON DELETE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $this->addSql("CREATE TABLE voiture (
            id INT AUTO_INCREMENT PRIMARY KEY,
            marque VARCHAR(100) NOT NULL,
            modele VARCHAR(100) NOT NULL,
            annee INT NOT NULL,
            prix_par_jour FLOAT NOT NULL,
            statut ENUM('disponible', 'louee', 'maintenance') DEFAULT 'disponible',
            nombre_places INT,
            type_carburant VARCHAR(50),
            photo_principale VARCHAR(255),
            description TEXT
        ) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $this->addSql("CREATE TABLE photo_voiture (
            id INT AUTO_INCREMENT PRIMARY KEY,
            voiture_id INT NOT NULL,
            url VARCHAR(255) NOT NULL,
            CONSTRAINT fk_photo_voiture
                FOREIGN KEY (voiture_id) REFERENCES voiture(id)
                ON DELETE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $this->addSql("CREATE TABLE reservation (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id INT NOT NULL,
            voiture_id INT NOT NULL,
            date_debut DATE NOT NULL,
            date_fin DATE NOT NULL,
            montant FLOAT NOT NULL,
            statut ENUM('en_attente', 'confirmee', 'annulee') DEFAULT 'en_attente',
            CONSTRAINT fk_res_client
                FOREIGN KEY (client_id) REFERENCES client(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_res_voiture
                FOREIGN KEY (voiture_id) REFERENCES voiture(id)
                ON DELETE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        // Drop tables in reverse order of dependencies
        $this->addSql('DROP TABLE IF EXISTS reservation');
        $this->addSql('DROP TABLE IF EXISTS photo_voiture');
        $this->addSql('DROP TABLE IF EXISTS voiture');
        $this->addSql('DROP TABLE IF EXISTS administrateur');
        $this->addSql('DROP TABLE IF EXISTS client');
        $this->addSql('DROP TABLE IF EXISTS utilisateur');
    }
}

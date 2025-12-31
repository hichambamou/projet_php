-- SQL script to insert initial admin user
-- Note: The password '12345678' should be hashed using Symfony's password hasher
-- This script inserts the plain password for reference, but in production,
-- you should use the Symfony command: php bin/console app:create-admin admin@maroki-cars.com 12345678 admin

USE location;

-- Insert admin user
-- WARNING: This inserts a plain password. In production, use the Symfony command instead.
INSERT INTO utilisateur (nom, email, mot_de_passe, role)
VALUES ('admin', 'admin@maroki-cars.com', '12345678', 'ADMIN');

-- Get the ID of the inserted user
SET @admin_user_id = LAST_INSERT_ID();

-- Insert into administrateur table
INSERT INTO administrateur (id)
VALUES (@admin_user_id);

-- Verify the insertion
SELECT * FROM utilisateur WHERE email = 'admin@maroki-cars.com';


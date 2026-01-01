# Database Implementation Guide

This guide provides step-by-step instructions to implement the database schema for the car rental (location) system.

## Overview

The database schema consists of the following tables:
- `utilisateur` - Base user table with role (CLIENT or ADMIN)
- `client` - Client-specific information (references utilisateur)
- `administrateur` - Admin table (references utilisateur)
- `voiture` - Car information
- `photo_voiture` - Car photos (references voiture)
- `reservation` - Car reservations (references client and voiture)

## Prerequisites

1. MySQL/MariaDB database server installed and running
2. Symfony project set up with Doctrine ORM
3. Database connection configured in `.env` file

## Step-by-Step Implementation

### Step 1: Configure Database Connection

1. Open or create `.env` file in the project root
2. Set the `DATABASE_URL` environment variable:

```env
DATABASE_URL="mysql://username:password@127.0.0.1:3306/location?serverVersion=8.0&charset=utf8mb4"
```

Replace:
- `username` with your MySQL username
- `password` with your MySQL password
- `location` with your database name (or keep it as `location`)

### Step 2: Create the Database

You can create the database in two ways:

**Option A: Using MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE location;
USE location;
exit;
```

**Option B: Using Symfony Console**
```bash
php bin/console doctrine:database:create
```

### Step 3: Run Migrations

The migration file `migrations/Version20251228154924.php` already contains the table creation SQL matching your schema.

Run the migration:

```bash
php bin/console doctrine:migrations:migrate
```

This will create all the tables with the correct structure, including:
- All columns with proper types
- ENUM types for role and statut fields
- Foreign key constraints with CASCADE delete
- Proper indexes

### Step 4: Create Initial Admin User

You have two options:

**Option A: Using Symfony Command (Recommended - Password is Hashed)**
```bash
php bin/console app:create-admin admin@maroki-cars.com 12345678 admin
```

This command will:
- Create a `Utilisateur` record with role 'ADMIN'
- Hash the password using Symfony's password hasher
- Create the corresponding `Administrateur` record
- Link them properly

**Option B: Using SQL Script (For Testing Only)**
```bash
mysql -u root -p location < database/init_admin.sql
```

⚠️ **Warning**: The SQL script inserts a plain password. This should only be used for development/testing. For production, always use the Symfony command which properly hashes passwords.

### Step 5: Verify the Implementation

1. **Check Tables Created:**
```bash
mysql -u root -p location -e "SHOW TABLES;"
```

You should see:
- utilisateur
- client
- administrateur
- voiture
- photo_voiture
- reservation

2. **Verify Admin User:**
```bash
mysql -u root -p location -e "SELECT * FROM utilisateur WHERE email = 'admin@maroki-cars.com';"
```

3. **Check Foreign Key Constraints:**
```bash
mysql -u root -p location -e "SHOW CREATE TABLE client;"
mysql -u root -p location -e "SHOW CREATE TABLE administrateur;"
```

### Step 6: Test the Application

1. Start the Symfony development server:
```bash
symfony server:start
# or
php -S localhost:8000 -t public
```

2. Try to log in with the admin credentials:
   - Email: `admin@maroki-cars.com`
   - Password: `12345678`

## Entity Structure

The entities have been updated to match the SQL schema:

- **Utilisateur**: Base entity with id, nom, email, mot_de_passe, role
- **Client**: Has a OneToOne relationship with Utilisateur, includes adresse and telephone
- **Administrateur**: Has a OneToOne relationship with Utilisateur
- **Voiture**: Car entity with all specified fields
- **PhotoVoiture**: Photo entity linked to Voiture
- **Reservation**: Reservation entity linked to Client and Voiture

## Important Notes

1. **Password Hashing**: Always use Symfony's `UserPasswordHasherInterface` to hash passwords. Never store plain passwords.

2. **Entity Relationships**: 
   - Client and Administrateur now use composition (OneToOne) instead of inheritance
   - They delegate UserInterface methods to their associated Utilisateur
   - When creating Client/Administrateur, you must first create and persist the Utilisateur

3. **ENUM Types**: The database uses MySQL ENUM types for:
   - `utilisateur.role`: 'CLIENT' or 'ADMIN'
   - `voiture.statut`: 'disponible', 'louee', or 'maintenance'
   - `reservation.statut`: 'en_attente', 'confirmee', or 'annulee'

4. **Foreign Keys**: All foreign keys use `ON DELETE CASCADE`, meaning:
   - Deleting a Utilisateur will delete the associated Client/Administrateur
   - Deleting a Voiture will delete all associated PhotoVoiture and Reservation records
   - Deleting a Client will delete all associated Reservation records

## Troubleshooting

### Migration Fails
- Check that the database exists
- Verify database connection in `.env`
- Check MySQL user has CREATE TABLE permissions

### Foreign Key Errors
- Ensure tables are created in the correct order (migration handles this)
- Check that referenced tables exist before creating foreign keys

### Password Issues
- Always use the Symfony command to create users with hashed passwords
- Plain passwords in SQL won't work with Symfony's security system

### Entity Not Found Errors
- Clear Symfony cache: `php bin/console cache:clear`
- Regenerate Doctrine proxies: `php bin/console doctrine:orm:generate-proxies`

## Next Steps

After implementing the database:

1. Test user registration (creates Client)
2. Test admin login
3. Create some test cars (Voiture)
4. Test reservation creation
5. Verify all relationships work correctly

## Database Schema Summary

```
utilisateur (id, nom, email, mot_de_passe, role)
    ↓ (1:1)
client (id → utilisateur.id, adresse, telephone)
    ↓ (1:N)
reservation (id, client_id, voiture_id, date_debut, date_fin, montant, statut)

utilisateur (id, nom, email, mot_de_passe, role)
    ↓ (1:1)
administrateur (id → utilisateur.id)

voiture (id, marque, modele, annee, prix_par_jour, statut, nombre_places, type_carburant, photo_principale, description)
    ↓ (1:N)
photo_voiture (id, voiture_id, url)
    ↓ (1:N)
reservation (id, client_id, voiture_id, date_debut, date_fin, montant, statut)
```

## Support

If you encounter issues:
1. Check Symfony logs: `var/log/dev.log`
2. Check database connection: `php bin/console doctrine:database:create --if-not-exists`
3. Verify migration status: `php bin/console doctrine:migrations:status`


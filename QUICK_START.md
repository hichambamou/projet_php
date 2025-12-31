# Quick Start Guide - Database Implementation

## Quick Steps Summary

### 1. Configure Database Connection
Edit `.env` file:
```env
DATABASE_URL="mysql://username:password@127.0.0.1:3306/location?serverVersion=8.0&charset=utf8mb4"
```

### 2. Create Database
```bash
php bin/console doctrine:database:create
```

### 3. Run Migrations
```bash
php bin/console doctrine:migrations:migrate
```

### 4. Create Admin User (Recommended)
```bash
php bin/console app:create-admin admin@maroki-cars.com 12345678 admin
```

### 5. Verify
```bash
# Check tables
mysql -u root -p location -e "SHOW TABLES;"

# Check admin user
mysql -u root -p location -e "SELECT * FROM utilisateur WHERE email = 'admin@maroki-cars.com';"
```

## What Was Changed

### Entities Updated
- ✅ `Utilisateur` - Added `role` field with ENUM type
- ✅ `Client` - Changed from inheritance to composition (OneToOne with Utilisateur)
- ✅ `Administrateur` - Changed from inheritance to composition (OneToOne with Utilisateur)
- ✅ `Voiture` - Updated `statut` to use ENUM type
- ✅ `Reservation` - Updated `statut` to use ENUM type

### Controllers Updated
- ✅ `RegistrationController` - Updated to create Utilisateur first
- ✅ `ClientController` - Updated to create Utilisateur first
- ✅ `AdminController` - Updated to create Utilisateur first and handle editing
- ✅ `CreateAdminCommand` - Updated to create Utilisateur first

### Files Created
- ✅ `database/init_admin.sql` - SQL script for initial admin (development only)
- ✅ `DATABASE_IMPLEMENTATION_GUIDE.md` - Complete implementation guide
- ✅ `QUICK_START.md` - This quick reference

## Important Notes

1. **Password Hashing**: Always use the Symfony command to create users. The SQL script inserts plain passwords which won't work with Symfony's security system.

2. **Entity Structure**: Client and Administrateur now have a OneToOne relationship with Utilisateur instead of extending it. They delegate UserInterface methods to Utilisateur.

3. **Creating Users**: When creating Client/Administrateur:
   - First create and persist Utilisateur
   - Flush to get the ID
   - Set the Client/Administrateur ID to match
   - Then persist Client/Administrateur

## Testing

After implementation, test:
1. Admin login: `admin@maroki-cars.com` / `12345678`
2. User registration (creates Client)
3. Creating cars
4. Making reservations

## Need Help?

See `DATABASE_IMPLEMENTATION_GUIDE.md` for detailed instructions and troubleshooting.


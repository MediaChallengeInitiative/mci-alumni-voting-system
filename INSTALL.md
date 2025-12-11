# Media Challenge Awards 2025 - Alumni Voting System
## Installation & Configuration Guide

---

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Installation Steps](#installation-steps)
3. [Database Setup](#database-setup)
4. [Configuration](#configuration)
5. [Security Features](#security-features)
6. [Troubleshooting HTTP 500 Errors](#troubleshooting-http-500-errors)
7. [Admin Panel Access](#admin-panel-access)
8. [Voter Registration & Login](#voter-registration--login)

---

## System Requirements

- PHP 7.4 or higher (PHP 8.x recommended)
- MySQL 5.7 or MariaDB 10.x
- Apache/Nginx web server
- XAMPP, WAMP, or similar local development environment

---

## Installation Steps

### Step 1: Upload Files
Upload all files to your web server's document root or subdirectory.

### Step 2: Set File Permissions
```bash
# Set proper permissions for the project
chmod -R 755 /path/to/alumnivotingsystem
chmod 644 /path/to/alumnivotingsystem/*.php
chmod 755 /path/to/alumnivotingsystem/images
chmod 755 /path/to/alumnivotingsystem/logs
```

### Step 3: Configure the Database Connection
Edit `/includes/conn.php` and `/admin/includes/conn.php` with your database credentials:

```php
$conn = new mysqli('localhost', 'your_username', 'your_password', 'alumni_voting_db');
```

---

## Database Setup

### Option 1: Run the Complete Schema (Recommended for new installations)
1. Open phpMyAdmin or MySQL CLI
2. Run the SQL file: `db/complete_schema_update.sql`

### Option 2: Update Existing Database
If you have an existing installation, run:
```sql
SOURCE /path/to/db/complete_schema_update.sql;
```

### Tables Created:
- `admin` - Administrator accounts
- `positions` - Award categories
- `candidates` - Nominees
- `voters` - Registered voters
- `votes` - Cast votes
- `device_registry` - Device tracking for security
- `voter_sessions` - Active session management
- `login_attempts` - Login attempt tracking
- `error_log` - Application errors

---

## Configuration

### Secret Code Configuration
The secret code is configured in `/includes/config.php`:

```php
define('VOTER_SECRET_CODE', 'MCI2025AWARDS');
```

**IMPORTANT:** Change this to your own secret code before deployment!

### Other Configuration Options
Located in `/includes/config.php`:

```php
// Password hashing cost (higher = more secure but slower)
define('PASSWORD_COST', 12);

// Session timeout in seconds (2 hours default)
define('SESSION_TIMEOUT', 7200);

// Maximum login attempts before lockout
define('MAX_LOGIN_ATTEMPTS', 5);

// Lockout duration in seconds (15 minutes)
define('LOCKOUT_DURATION', 900);

// Default password for voters added via admin panel
define('DEFAULT_PASSWORD', 'AwardsNight2025');
```

---

## Security Features

### 1. Prepared Statements (SQL Injection Prevention)
All database queries use parameterized prepared statements.

### 2. Secret Code Validation
- Required for both voter registration and login
- Server-side validation only (never exposed in frontend)
- Failed attempts are logged

### 3. Device Fingerprinting
- Each voter is bound to a single device
- Prevents multiple logins from different devices
- Admin can clear device binding if needed

### 4. Session Security
- Secure session tokens
- Session regeneration on login
- Automatic timeout after 2 hours
- Device fingerprint validation per request

### 5. Password Security
- BCrypt hashing with cost factor 12
- Minimum 8 characters for voter passwords

### 6. Error Logging
All errors are logged to `/logs/` directory (protected by .htaccess)

---

## Troubleshooting HTTP 500 Errors

### Common Causes and Fixes:

#### 1. File Permission Issues
```bash
# Fix permissions for all PHP files
chmod 644 /path/to/alumnivotingsystem/*.php
chmod 644 /path/to/alumnivotingsystem/**/*.php

# Make sure logs directory is writable
chmod 755 /path/to/alumnivotingsystem/logs
```

#### 2. Session Directory Not Writable
```bash
# Check PHP session directory permissions
php -i | grep session.save_path
chmod 755 /path/to/session/directory
```

#### 3. Database Connection Errors
- Verify database credentials in `includes/conn.php`
- Ensure MySQL server is running
- Check that `alumni_voting_db` database exists

#### 4. PHP Errors
Check the PHP error log:
```bash
tail -50 /path/to/php_error_log
```

#### 5. Missing config.php
Ensure `/includes/config.php` exists and is properly configured.

---

## Admin Panel Access

### Default Credentials:
- **URL:** `http://your-domain/alumnivotingsystem/admin/`
- **Username:** `admin`
- **Password:** `admin123`

**IMPORTANT:** Change the admin password immediately after first login!

### Admin Features:
- Dashboard with voting statistics
- Manage voters (add, edit, delete, reset password, clear device)
- Manage positions (award categories)
- Manage candidates (nominees)
- View votes and generate reports
- Print reports

---

## Voter Registration & Login

### Voter Signup Process:
1. Go to: `http://your-domain/alumnivotingsystem/signup.php`
2. Fill in:
   - Username
   - First Name
   - Last Name
   - Password (min 8 characters)
   - Confirm Password
   - **Secret Code** (provided by MCI)
3. System auto-generates Voter ID in format: `MCIA{FirstLetter}{First2OfLast}25`
   - Example: John Balungi → `MCIAJBA25`

### Voter Login Process:
1. Go to: `http://your-domain/alumnivotingsystem/`
2. Enter:
   - Voter ID
   - Password
   - **Secret Code**
3. Vote on the ballot

### Voter ID Format:
- Prefix: `MCIA`
- First letter of first name (uppercase)
- First two letters of last name (uppercase)
- Suffix: `25`
- Duplicates get numeric suffix: `MCIAJBA252`, `MCIAJBA253`, etc.

---

## File Structure

```
alumnivotingsystem/
├── admin/                    # Admin panel
│   ├── includes/            # Admin includes
│   │   ├── session.php      # Secure session handler
│   │   ├── conn.php         # Database connection
│   │   └── *.php            # Other includes
│   ├── login.php            # Admin login handler
│   ├── home.php             # Dashboard
│   ├── voters.php           # Voter management
│   └── *.php                # Other admin pages
├── includes/                 # Public includes
│   ├── config.php           # Main configuration
│   ├── conn.php             # Database connection
│   └── session.php          # Voter session handler
├── db/                       # Database scripts
│   └── complete_schema_update.sql
├── logs/                     # Error logs (protected)
├── images/                   # Uploaded images
├── index.php                 # Voter login page
├── signup.php                # Voter registration
├── signup_process.php        # Registration handler
├── login.php                 # Login handler
├── home.php                  # Voting ballot
├── submit_ballot.php         # Vote submission
└── vote_success.php          # Success page
```

---

## Support

For issues or questions:
1. Check the error logs in `/logs/`
2. Review PHP error log
3. Ensure all database tables are created
4. Verify file permissions

---

*Media Challenge Initiative - 2025 Awards*

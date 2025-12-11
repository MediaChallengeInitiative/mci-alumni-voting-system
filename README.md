# MCI Alumni Voting System

Secure PHP voting system for **Media Challenge Initiative Alumni Awards 2025**.

## Features

- **Secret Code Authentication** - Voters must enter a shared secret code to register and login
- **Device Fingerprinting** - Each voter is bound to a single device to prevent multiple voting
- **Auto-Generated Voter IDs** - Format: `MCIA{FirstLetter}{First2OfLast}25` (e.g., John Balungi → MCIAJBA25)
- **Secure Password Hashing** - BCrypt with cost factor 12
- **SQL Injection Protection** - All queries use prepared statements
- **Session Security** - Token-based sessions with automatic timeout
- **Comprehensive Admin Panel** - Manage voters, candidates, positions, and view results
- **Real-time Vote Tracking** - Dashboard with voting statistics and charts
- **Error Logging** - All errors logged for debugging

## Requirements

- PHP 7.4 or higher (PHP 8.x recommended)
- MySQL 5.7+ or MariaDB 10.x
- Apache/Nginx web server

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/MediaChallengeInitiative/mci-alumni-voting-system.git
   ```

2. **Import the database**
   ```bash
   mysql -u root -p alumni_voting_db < db/complete_schema_update.sql
   ```

3. **Configure database connection**
   Edit `includes/conn.php` and `admin/includes/conn.php` with your database credentials.

4. **Configure the secret code**
   Edit `includes/config.php` and change `VOTER_SECRET_CODE`.

5. **Set file permissions**
   ```bash
   chmod -R 755 /path/to/project
   chmod 755 logs/ images/
   ```

## Default Credentials

### Admin Panel
- **URL:** `/admin/`
- **Username:** `admin`
- **Password:** `admin123`

### Voter Login
- **Secret Code:** `MCI2025AWARDS` (configurable)
- **Default Password:** `AwardsNight2025`

## Security Features

- Secret Code Authentication
- Device Fingerprinting & Binding
- Prepared Statements (SQL Injection Prevention)
- BCrypt Password Hashing
- Session Token Validation
- Comprehensive Error Logging

## License

© 2025 Media Challenge Initiative. All rights reserved.

Website: https://mciug.org

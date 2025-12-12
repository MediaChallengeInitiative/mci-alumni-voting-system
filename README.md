# MCI Alumni Voting System

Secure PHP voting system for **Media Challenge Initiative Alumni Awards 2025**.

## Features

- **Simple Username/Password Login** - Easy authentication for all voters
- **Device Fingerprinting** - Each voter is bound to a single device to prevent multiple voting
- **Auto-Generated Voter IDs** - System generates unique IDs for admin tracking (e.g., Emmanuel Bahindi → MCIAEBA25)
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

4. **Set file permissions**
   ```bash
   chmod -R 755 /path/to/project
   chmod 755 logs/ images/
   ```

## Default Credentials

### Admin Panel
- **URL:** `/admin/`
- **Username:** `admin`
- **Password:** `admin123`

### Voter Registration & Login
Voters can register and login using:
- **Username** - Chosen during registration
- **Password** - Created during registration

## How to Vote

### Step 1: Register
1. Go to the voting website
2. Click "Don't have an account? Register here"
3. Fill in:
   - Username (choose your own)
   - First Name
   - Last Name
   - Password (minimum 8 characters)
   - Confirm Password
4. Click "Create Account"

### Step 2: Login
1. Enter your Username
2. Enter your Password
3. Click "Cast Your Vote"

### Step 3: Vote
1. Browse the award categories
2. Select your preferred candidate for each position
3. Submit your votes
4. You will receive a confirmation once your votes are recorded

## Security Features

- Device Fingerprinting & Binding (one device per voter)
- Prepared Statements (SQL Injection Prevention)
- BCrypt Password Hashing
- Session Token Validation
- Comprehensive Error Logging

## Database Updates for Production

If upgrading from a previous version, run this SQL:
```sql
ALTER TABLE voters ADD COLUMN username VARCHAR(50) NULL AFTER voters_id;
```

## License

© 2025 Media Challenge Initiative. All rights reserved.

Website: https://mciug.org

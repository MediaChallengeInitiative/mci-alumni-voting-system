# Awards Night 2025 Voting System
## Complete Technical Documentation

**Version:** 2.0
**Last Updated:** December 10, 2025
**Developed for:** Media Challenge Initiative

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Technology Stack](#2-technology-stack)
3. [System Architecture](#3-system-architecture)
4. [Database Schema](#4-database-schema)
5. [Security Features](#5-security-features)
6. [User Roles & Permissions](#6-user-roles--permissions)
7. [Core Features](#7-core-features)
8. [File Structure](#8-file-structure)
9. [Installation Guide](#9-installation-guide)
10. [Configuration](#10-configuration)
11. [API Endpoints](#11-api-endpoints)
12. [Troubleshooting](#12-troubleshooting)

---

## 1. System Overview

The Awards Night 2025 Voting System is a secure, web-based electronic voting platform designed for the Media Challenge Initiative's annual alumni awards ceremony. The system enables registered voters to cast their votes for nominees across multiple award categories while ensuring vote integrity, preventing fraud, and providing a delightful user experience.

### Key Objectives
- **Security:** Prevent voter fraud through device binding and one-time voting
- **Automation:** Auto-generate voter credentials for easy management
- **User Experience:** Modern, responsive UI with celebratory animations
- **Transparency:** Allow voters to review their submitted ballots
- **Administration:** Comprehensive admin panel for voter and nominee management

---

## 2. Technology Stack

### Backend
| Component | Technology | Version |
|-----------|------------|---------|
| Server-Side Language | PHP | 7.4+ |
| Database | MySQL/MariaDB | 5.7+ |
| Web Server | Apache (XAMPP) | 2.4+ |
| Session Management | PHP Sessions | Native |
| Password Hashing | bcrypt | Cost 12 |

### Frontend
| Component | Technology | Version |
|-----------|------------|---------|
| CSS Framework | Bootstrap | 3.3.7 |
| JavaScript Library | jQuery | 3.x |
| Admin Theme | AdminLTE | 2.x |
| Icons | Font Awesome | 4.x |
| Typography | Google Fonts (Poppins) | - |
| Form Styling | iCheck | 1.0.1 |
| Charts | Chart.js | - |
| Confetti Animation | canvas-confetti | 1.6.0 |

### Additional Libraries
- DataTables (Admin data management)
- TCPDF (PDF generation)
- Select2 (Enhanced dropdowns)

---

## 3. System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │  Voter UI    │  │  Admin UI    │  │  Mobile UI   │          │
│  │  (Bootstrap) │  │  (AdminLTE)  │  │  (Responsive)│          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     APPLICATION LAYER                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │ Authentication│  │   Voting     │  │    Admin     │          │
│  │   Module     │  │   Module     │  │   Module     │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   Device     │  │   Session    │  │  Reporting   │          │
│  │  Tracking    │  │  Management  │  │   Module     │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       DATA LAYER                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    MySQL Database                         │  │
│  │  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ │  │
│  │  │ voters │ │ votes  │ │candidates│ │positions│ │ admin │ │  │
│  │  └────────┘ └────────┘ └────────┘ └────────┘ └────────┘ │  │
│  │  ┌────────────────┐ ┌────────────────┐                   │  │
│  │  │device_registry │ │ voter_sessions │                   │  │
│  │  └────────────────┘ └────────────────┘                   │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 4. Database Schema

### Tables Overview

#### 4.1 `voters` - Registered Voters
```sql
CREATE TABLE voters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    voters_id VARCHAR(15) NOT NULL UNIQUE,      -- Auto-generated ID (e.g., MCIAEBA25)
    password VARCHAR(60) NOT NULL,               -- bcrypt hashed
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    photo VARCHAR(150),
    device_fingerprint VARCHAR(255),             -- Device binding hash
    device_info TEXT,                            -- Device details JSON
    has_voted TINYINT(1) DEFAULT 0,             -- Voting status flag
    voted_at DATETIME,                           -- Vote submission timestamp
    session_token VARCHAR(255),                  -- Active session token
    login_time DATETIME,                         -- Last login time
    is_logged_in TINYINT(1) DEFAULT 0,          -- Login status
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

#### 4.2 `positions` - Award Categories
```sql
CREATE TABLE positions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    description VARCHAR(50) NOT NULL,            -- Category name
    max_vote INT DEFAULT 1,                      -- Max selections allowed
    priority INT                                 -- Display order
);
```

#### 4.3 `candidates` - Nominees
```sql
CREATE TABLE candidates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    position_id INT NOT NULL,                    -- Foreign key to positions
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    photo VARCHAR(150),
    platform TEXT,                               -- Bio/achievements
    FOREIGN KEY (position_id) REFERENCES positions(id)
);
```

#### 4.4 `votes` - Cast Votes
```sql
CREATE TABLE votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    voters_id INT NOT NULL,                      -- Foreign key to voters
    candidate_id INT NOT NULL,                   -- Foreign key to candidates
    position_id INT NOT NULL,                    -- Foreign key to positions
    FOREIGN KEY (voters_id) REFERENCES voters(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id),
    FOREIGN KEY (position_id) REFERENCES positions(id)
);
```

#### 4.5 `device_registry` - Device Tracking
```sql
CREATE TABLE device_registry (
    id INT PRIMARY KEY AUTO_INCREMENT,
    device_fingerprint VARCHAR(255) NOT NULL UNIQUE,
    device_info TEXT,
    voter_id INT,
    first_used_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_used_at DATETIME,
    is_blocked TINYINT(1) DEFAULT 0,
    FOREIGN KEY (voter_id) REFERENCES voters(id)
);
```

#### 4.6 `voter_sessions` - Session Management
```sql
CREATE TABLE voter_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    voter_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    device_fingerprint VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (voter_id) REFERENCES voters(id)
);
```

#### 4.7 `admin` - Administrator Accounts
```sql
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(60) NOT NULL,               -- bcrypt hashed
    firstname VARCHAR(50),
    lastname VARCHAR(50),
    photo VARCHAR(150),
    created_on DATE
);
```

---

## 5. Security Features

### 5.1 Voter ID Auto-Generation
- **Format:** `MCIA{FirstLetterOfFirstName}{FirstTwoLettersOfLastName}25`
- **Example:** Emmanuel Bahindi → `MCIAEBA25`
- **Collision Handling:** Numeric suffix added for duplicates (MCIAEBA252, MCIAEBA253)
- **Case:** All uppercase for consistency

### 5.2 Password Security
- **Default Password:** `AwardsNight2025`
- **Hashing Algorithm:** bcrypt with cost factor 12
- **Admin Reset:** Administrators can reset passwords to default

### 5.3 Device Binding
```
Device Fingerprint = SHA256(User-Agent + IP Address + Accept-Language)
```
- Each voter is bound to the first device they log in from
- Login attempts from different devices are blocked
- Admins can clear device bindings if needed

### 5.4 One-Time Voting
- `has_voted` flag prevents multiple vote submissions
- Vote timestamp recorded for audit trail
- Session invalidated after vote submission

### 5.5 Session Security
- Session token regeneration on login
- Token validation on every request
- Automatic logout after vote submission
- 2-hour session expiry

### 5.6 SQL Injection Prevention
- All database queries use prepared statements
- Parameter binding for user inputs
- Input sanitization and validation

---

## 6. User Roles & Permissions

### 6.1 Voter
| Permission | Description |
|------------|-------------|
| Login | Access system with Voter ID and password |
| Vote | Cast votes in all categories (one time only) |
| Preview | Review selections before submission |
| View Ballot | View submitted ballot after voting |

### 6.2 Administrator
| Permission | Description |
|------------|-------------|
| Manage Voters | Add, edit, delete voter accounts |
| Manage Positions | Create and organize award categories |
| Manage Candidates | Add nominees to categories |
| Reset Passwords | Reset voter passwords to default |
| Clear Devices | Remove device bindings |
| View Results | Access real-time voting statistics |
| Print Reports | Generate printable vote tallies |

---

## 7. Core Features

### 7.1 Voter Registration (Admin)
1. Admin enters voter's first name and last name
2. System auto-generates Voter ID
3. Default password assigned and hashed
4. Credentials displayed for distribution

### 7.2 Voter Authentication
1. Voter enters Voter ID and password
2. System validates credentials
3. Device fingerprint generated and checked
4. Session created with unique token
5. Redirect to voting page or ballot view

### 7.3 Voting Process
1. Display all categories with nominees
2. Voter selects one candidate per category
3. Preview ballot before submission
4. Submit vote with confirmation
5. Confetti celebration animation
6. Auto-logout with redirect

### 7.4 Ballot Review
- Voters can log in after voting
- View-only mode displays submitted ballot
- Shows voting timestamp
- Security confirmation message

### 7.5 Admin Dashboard
- Real-time voting statistics
- Chart visualizations per category
- Voter turnout tracking
- Vote management tools

---

## 8. File Structure

```
/alumnivotingsystem/
├── index.php                 # Voter login page
├── login.php                 # Authentication handler
├── logout.php                # Session termination
├── home.php                  # Voting interface
├── submit_ballot.php         # Vote submission
├── preview.php               # AJAX ballot preview
├── vote_success.php          # Success page with confetti
│
├── includes/
│   ├── conn.php              # Database connection
│   ├── session.php           # Session validation
│   ├── header.php            # HTML head
│   ├── navbar.php            # Navigation
│   ├── footer.php            # Footer
│   ├── scripts.php           # JavaScript includes
│   ├── ballot_modal.php      # Modal dialogs
│   └── slugify.php           # URL helper
│
├── admin/
│   ├── index.php             # Admin login redirect
│   ├── login.php             # Admin authentication
│   ├── home.php              # Dashboard
│   ├── voters.php            # Voter management
│   ├── voters_add.php        # Add voter
│   ├── voters_edit.php       # Edit voter
│   ├── voters_delete.php     # Delete voter
│   ├── voters_reset_password.php  # Reset password
│   ├── voters_clear_device.php    # Clear device binding
│   ├── positions.php         # Category management
│   ├── candidates.php        # Nominee management
│   ├── votes.php             # Vote viewing
│   ├── ballot.php            # Ballot ordering
│   └── includes/             # Admin includes
│
├── assets/
│   └── css/
│       └── media-challenge.css  # Custom theme
│
├── db/
│   ├── media_challenge_awards_2025.sql  # Database schema
│   └── migration_device_tracking.sql    # Security migration
│
├── images/                   # User/candidate photos
├── bower_components/         # Frontend libraries
├── plugins/                  # jQuery plugins
├── dist/                     # AdminLTE assets
└── docs/                     # Documentation
```

---

## 9. Installation Guide

### Prerequisites
- XAMPP (Apache + MySQL + PHP 7.4+)
- Web browser (Chrome, Firefox, Safari, Edge)

### Step 1: Database Setup
```bash
# Access MySQL
mysql -u root

# Create database
CREATE DATABASE alumni_voting_db;
USE alumni_voting_db;

# Import schema
SOURCE /path/to/db/media_challenge_awards_2025.sql;
SOURCE /path/to/db/migration_device_tracking.sql;
```

### Step 2: Configuration
Edit `includes/conn.php`:
```php
$conn = new mysqli('localhost', 'root', '', 'alumni_voting_db');
```

### Step 3: File Permissions
```bash
chmod 644 *.php
chmod 755 images/
chmod 755 admin/
```

### Step 4: Access System
- Voter Portal: `http://localhost/alumnivotingsystem/`
- Admin Panel: `http://localhost/alumnivotingsystem/admin/`

---

## 10. Configuration

### Database Connection (`includes/conn.php`)
```php
$conn = new mysqli(
    'localhost',    // Host
    'root',         // Username
    '',             // Password
    'alumni_voting_db'  // Database
);
```

### Election Title (`admin/config.ini`)
```ini
election_title = "2025 Media Challenge Awards"
```

### Session Timeout (`includes/session.php`)
```php
ini_set('session.gc_maxlifetime', 3600);  // 1 hour
```

---

## 11. API Endpoints

### Voter Authentication
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/login.php` | POST | Authenticate voter |
| `/logout.php` | GET | End session |

### Voting
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/preview.php` | POST | Preview ballot (AJAX) |
| `/submit_ballot.php` | POST | Submit vote |

### Admin API
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/admin/voters_add.php` | POST | Add voter |
| `/admin/voters_edit.php` | POST | Update voter |
| `/admin/voters_delete.php` | POST | Delete voter |
| `/admin/voters_reset_password.php` | POST | Reset password |
| `/admin/voters_clear_device.php` | POST | Clear device |
| `/admin/voters_row.php` | POST | Get voter data |

---

## 12. Troubleshooting

### Common Issues

#### HTTP 500 Error
```bash
# Check file permissions
chmod 644 /path/to/file.php

# Check PHP error log
tail -f /Applications/XAMPP/xamppfiles/logs/php_error_log
```

#### Database Connection Failed
- Verify MySQL is running
- Check credentials in `conn.php`
- Ensure database exists

#### Session Issues
- Clear browser cookies
- Check session directory permissions
- Verify session configuration

#### Device Binding Issues
- Admin can clear device via Voters panel
- Click "Clear Device" button for affected voter

### Support Contacts
- Technical Support: [Your IT Team]
- System Administrator: [Admin Contact]

---

## Appendix A: Default Credentials

### Admin Account
- **Username:** admin
- **Password:** password

### Test Voter
- **Voter ID:** MCIAEBA25
- **Password:** AwardsNight2025

---

## Appendix B: Security Checklist

- [ ] Change default admin password
- [ ] Configure HTTPS in production
- [ ] Set proper file permissions
- [ ] Enable MySQL password
- [ ] Configure firewall rules
- [ ] Regular database backups
- [ ] Monitor access logs

---

*Document generated for Media Challenge Initiative*
*Awards Night 2025 Voting System v2.0*

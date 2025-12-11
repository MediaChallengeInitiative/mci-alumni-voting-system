<?php
/**
 * Secure Configuration File for Alumni Voting System
 * Media Challenge Awards 2025
 *
 * IMPORTANT: This file contains sensitive configuration.
 * Keep this file secure and never expose it publicly.
 */

// Prevent direct access
if (!defined('VOTING_SYSTEM')) {
    die('Direct access not permitted');
}

// =====================================================
// DATABASE CONFIGURATION
// =====================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'alumni_voting_db');

// =====================================================
// SECRET CODE FOR VOTER REGISTRATION & LOGIN
// =====================================================
// This is the shared secret code that voters must enter to register and login
// Change this to your desired secret code
define('VOTER_SECRET_CODE', 'MCI2025AWARDS');

// =====================================================
// SECURITY SETTINGS
// =====================================================
// Password hashing cost (higher = more secure but slower)
define('PASSWORD_COST', 12);

// Session timeout in seconds (2 hours)
define('SESSION_TIMEOUT', 7200);

// Maximum login attempts before lockout
define('MAX_LOGIN_ATTEMPTS', 5);

// Lockout duration in seconds (15 minutes)
define('LOCKOUT_DURATION', 900);

// =====================================================
// APPLICATION SETTINGS
// =====================================================
define('APP_NAME', 'Media Challenge Awards 2025');
define('APP_VERSION', '2.0.0');
define('DEFAULT_PASSWORD', 'AwardsNight2025');

// Voter ID format settings
define('VOTER_ID_PREFIX', 'MCIA');
define('VOTER_ID_SUFFIX', '25');

// =====================================================
// ERROR LOGGING
// =====================================================
define('ENABLE_ERROR_LOGGING', true);
define('ERROR_LOG_PATH', __DIR__ . '/../logs/');

// =====================================================
// ERROR HANDLER
// =====================================================
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    if (ENABLE_ERROR_LOGGING) {
        $logDir = ERROR_LOG_PATH;
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . 'error_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $message = "[$timestamp] Error $errno: $errstr in $errfile on line $errline\n";

        error_log($message, 3, $logFile);
    }

    // Don't execute PHP's internal error handler for non-fatal errors
    if ($errno == E_USER_ERROR) {
        return true;
    }

    return false;
}

// Set custom error handler
set_error_handler('customErrorHandler');

// Exception handler
function customExceptionHandler($exception) {
    if (ENABLE_ERROR_LOGGING) {
        $logDir = ERROR_LOG_PATH;
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . 'exception_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $message = "[$timestamp] Exception: " . $exception->getMessage() .
                   " in " . $exception->getFile() .
                   " on line " . $exception->getLine() . "\n" .
                   "Stack trace:\n" . $exception->getTraceAsString() . "\n\n";

        error_log($message, 3, $logFile);
    }
}

set_exception_handler('customExceptionHandler');

// =====================================================
// HELPER FUNCTIONS
// =====================================================

/**
 * Generate a unique Voter ID
 * Format: MCIA{FirstLetterOfFirstName}{FirstTwoLettersOfLastName}25
 * Example: John Balungi → MCIAJBA25
 */
function generateVoterId($firstname, $lastname, $conn) {
    $firstLetter = strtoupper(substr(trim($firstname), 0, 1));
    $firstTwoOfLast = strtoupper(substr(trim($lastname), 0, 2));

    $baseVoterId = VOTER_ID_PREFIX . $firstLetter . $firstTwoOfLast . VOTER_ID_SUFFIX;
    $voterId = $baseVoterId;
    $counter = 1;

    // Check for duplicates and add numeric suffix if needed
    $stmt = $conn->prepare("SELECT id FROM voters WHERE voters_id = ?");
    $stmt->bind_param("s", $voterId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($result->num_rows > 0) {
        $counter++;
        $voterId = $baseVoterId . $counter;
        $stmt->bind_param("s", $voterId);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    $stmt->close();

    return $voterId;
}

/**
 * Validate secret code
 */
function validateSecretCode($code) {
    return $code === VOTER_SECRET_CODE;
}

/**
 * Generate device fingerprint
 */
function generateDeviceFingerprint() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';

    return hash('sha256', $userAgent . $ip . $acceptLanguage);
}

/**
 * Get device info for logging
 */
function getDeviceInfo() {
    return json_encode([
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Log error to custom log file
 */
function logError($message, $type = 'error') {
    if (!ENABLE_ERROR_LOGGING) return;

    $logDir = ERROR_LOG_PATH;
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . $type . '_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $logMessage = "[$timestamp] [$ip] $message\n";

    error_log($logMessage, 3, $logFile);
}

/**
 * Check if user is locked out
 */
function isLockedOut($identifier, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as attempts, MAX(attempt_time) as last_attempt
                            FROM login_attempts
                            WHERE identifier = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)");
    $lockoutDuration = LOCKOUT_DURATION;
    $stmt->bind_param("si", $identifier, $lockoutDuration);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($result && $result['attempts'] >= MAX_LOGIN_ATTEMPTS) {
        return true;
    }

    return false;
}

/**
 * Record login attempt
 */
function recordLoginAttempt($identifier, $success, $conn) {
    // First, check if the table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'login_attempts'");
    if ($tableCheck->num_rows == 0) {
        return; // Table doesn't exist, skip
    }

    if ($success) {
        // Clear previous failed attempts on successful login
        $stmt = $conn->prepare("DELETE FROM login_attempts WHERE identifier = ?");
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $stmt->close();
    } else {
        // Record failed attempt
        $stmt = $conn->prepare("INSERT INTO login_attempts (identifier, ip_address) VALUES (?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt->bind_param("ss", $identifier, $ip);
        $stmt->execute();
        $stmt->close();
    }
}
?>

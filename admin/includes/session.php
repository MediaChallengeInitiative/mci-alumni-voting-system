<?php
/**
 * Admin Session Handler
 * Secure session management with proper validation
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Session configuration for security
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.use_strict_mode', 1);

    session_start();
}

// Include database connection
include 'includes/conn.php';

// Check if admin is logged in
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

// Validate admin ID is numeric
$adminId = intval($_SESSION['admin']);
if ($adminId <= 0) {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Use prepared statement for security
$stmt = $conn->prepare("SELECT * FROM admin WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Validate user exists
if (!$user) {
    // Admin not found - invalid session
    session_destroy();
    header('Location: index.php');
    exit();
}

// Check session timeout (optional - 2 hours)
if (isset($_SESSION['admin_last_activity'])) {
    $inactive = time() - $_SESSION['admin_last_activity'];
    if ($inactive > 7200) { // 2 hours
        session_destroy();
        header('Location: index.php?timeout=1');
        exit();
    }
}
$_SESSION['admin_last_activity'] = time();

// Regenerate session ID periodically for security
if (!isset($_SESSION['admin_created'])) {
    $_SESSION['admin_created'] = time();
} elseif (time() - $_SESSION['admin_created'] > 1800) {
    // Regenerate session ID every 30 minutes
    session_regenerate_id(true);
    $_SESSION['admin_created'] = time();
}
?>

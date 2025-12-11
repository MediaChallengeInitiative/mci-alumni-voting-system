<?php
/**
 * Database Connection for Admin Panel
 * Production credentials for cPanel
 */

// Suppress connection errors from displaying (security)
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli('localhost', 'mciaorba_ebahindi', 'nm3H]Lg%2cmr', 'mciaorba_alumni_voting_db');

if ($conn->connect_error) {
    // Log error instead of displaying
    error_log("Database connection failed: " . $conn->connect_error);

    // Show user-friendly error
    die("<h3>Database Connection Error</h3><p>Unable to connect to the database. Please contact the administrator.</p>");
}

// Set charset for proper encoding
$conn->set_charset("utf8mb4");
?>

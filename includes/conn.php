<?php
/**
 * Database Connection
 * Production credentials for cPanel
 */

$conn = new mysqli('localhost', 'mciaorba_ebahindi', 'nm3H]Lg%2cmr', 'mciaorba_alumni_voting_db');

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed. Please contact the administrator.");
}

// Enable persistent connections and optimize settings
$conn->set_charset("utf8mb4");
?>

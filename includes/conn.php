<?php
/**
 * Database Connection
 * Production credentials for cPanel with detailed error logging
 */

// Database configuration
$db_host = 'localhost';
$db_user = 'mciaorba_ebahindi';
$db_pass = 'nm3H]Lg%2cmr';
$db_name = 'mciaorba_alumni_voting_db';

// Suppress default error display
mysqli_report(MYSQLI_REPORT_OFF);

// Attempt connection
$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check for connection errors with detailed logging
if ($conn->connect_error) {
    $error_code = $conn->connect_errno;
    $error_message = $conn->connect_error;

    // Log detailed error
    $log_message = date('[Y-m-d H:i:s]') . " Database Connection Error\n";
    $log_message .= "Error Code: $error_code\n";
    $log_message .= "Error Message: $error_message\n";
    $log_message .= "Host: $db_host\n";
    $log_message .= "User: $db_user\n";
    $log_message .= "Database: $db_name\n";
    $log_message .= "PHP Version: " . phpversion() . "\n";
    $log_message .= "-----------------------------------\n";

    error_log($log_message);

    // Determine user-friendly error message based on error code
    switch ($error_code) {
        case 1045:
            $user_message = "Access denied. Invalid database username or password.";
            break;
        case 1049:
            $user_message = "Database '$db_name' does not exist. Please create it first.";
            break;
        case 2002:
            $user_message = "Cannot connect to database server. Server may be down.";
            break;
        case 2003:
            $user_message = "Cannot reach database server at '$db_host'.";
            break;
        case 2005:
            $user_message = "Unknown database host '$db_host'.";
            break;
        case 2006:
            $user_message = "MySQL server has gone away. Please try again.";
            break;
        case 2013:
            $user_message = "Lost connection to MySQL server during query.";
            break;
        default:
            $user_message = "Database connection failed. Error code: $error_code";
    }

    // Display error (for debugging - remove in final production)
    die("
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #e74c3c; border-radius: 8px; background: #fdf2f2;'>
        <h3 style='color: #c0392b; margin-top: 0;'>Database Connection Error</h3>
        <p style='color: #333;'><strong>Issue:</strong> $user_message</p>
        <hr style='border: none; border-top: 1px solid #e74c3c; margin: 15px 0;'>
        <p style='color: #666; font-size: 13px;'><strong>Debug Info:</strong></p>
        <ul style='color: #666; font-size: 12px; background: #fff; padding: 15px 15px 15px 30px; border-radius: 4px;'>
            <li>Error Code: $error_code</li>
            <li>Host: $db_host</li>
            <li>User: $db_user</li>
            <li>Database: $db_name</li>
            <li>PHP Version: " . phpversion() . "</li>
        </ul>
        <p style='color: #999; font-size: 11px; margin-bottom: 0;'>Please check your database credentials and ensure the database exists.</p>
    </div>
    ");
}

// Set charset for proper encoding
$conn->set_charset("utf8mb4");

// Optional: Test if tables exist
$tables_check = $conn->query("SHOW TABLES LIKE 'voters'");
if ($tables_check && $tables_check->num_rows == 0) {
    error_log("Warning: 'voters' table not found in database. Schema may not be imported.");
}
?>

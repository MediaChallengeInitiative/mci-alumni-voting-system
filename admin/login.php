<?php
/**
 * Admin Login Handler
 * Secure authentication with prepared statements
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

// Include database connection
include 'includes/conn.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

if (isset($_POST['login'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Please enter both username and password';
        header('Location: index.php');
        exit();
    }

    try {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password, firstname, lastname FROM admin WHERE username = ? LIMIT 1");

        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows < 1) {
            $_SESSION['error'] = 'Cannot find account with that username';
        } else {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                $_SESSION['admin'] = $row['id'];
                $_SESSION['admin_username'] = $row['username'];
                $_SESSION['admin_created'] = time();
                $_SESSION['admin_last_activity'] = time();

                $stmt->close();
                header('Location: home.php');
                exit();
            } else {
                $_SESSION['error'] = 'Incorrect password';
            }
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Admin login error: " . $e->getMessage());
        $_SESSION['error'] = 'A system error occurred. Please try again.';
    }
} else {
    $_SESSION['error'] = 'Please enter admin credentials first';
}

header('Location: index.php');
exit();
?>

<?php
/**
 * Voter Registration Processing
 * Handles secure voter signup with secret code validation
 */

session_start();

// Define constant for config access
define('VOTING_SYSTEM', true);
include 'includes/config.php';
include 'includes/conn.php';

if (isset($_POST['signup'])) {
    // Get and sanitize inputs
    $username = trim($_POST['username']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validation array
    $errors = [];

    // Validate required fields
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 3 || strlen($username) > 30) {
        $errors[] = 'Username must be between 3 and 30 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }

    if (empty($firstname)) {
        $errors[] = 'First name is required';
    } elseif (strlen($firstname) < 2) {
        $errors[] = 'First name must be at least 2 characters';
    }

    if (empty($lastname)) {
        $errors[] = 'Last name is required';
    } elseif (strlen($lastname) < 2) {
        $errors[] = 'Last name must be at least 2 characters';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    // Check if username already exists
    $checkStmt = $conn->prepare("SELECT id FROM voters WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $errors[] = 'This username is already taken. Please choose a different one.';
    }
    $checkStmt->close();

    // If there are errors, redirect back
    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: signup.php');
        exit();
    }

    // Generate unique Voter ID
    $voterId = generateVoterId($firstname, $lastname, $conn);

    // Hash password securely
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_COST]);

    // Insert new voter using prepared statement
    $stmt = $conn->prepare("INSERT INTO voters (voters_id, username, password, firstname, lastname, photo, created_at) VALUES (?, ?, ?, ?, ?, '', NOW())");
    $stmt->bind_param("sssss", $voterId, $username, $hashedPassword, $firstname, $lastname);

    if ($stmt->execute()) {
        // Log successful registration
        logError("New voter registered: $voterId - $username ($firstname $lastname) from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown'), 'registration');

        $_SESSION['success'] = 'Account created successfully!<br>You can now login with your username <strong>' . htmlspecialchars($username) . '</strong> and password.';
        header('Location: index.php');
        exit();
    } else {
        // Log error
        logError("Registration failed for $username: " . $conn->error, 'error');

        $_SESSION['error'] = 'Registration failed. Please try again.';
        header('Location: signup.php');
        exit();
    }

    $stmt->close();
} else {
    $_SESSION['error'] = 'Please complete the registration form';
    header('Location: signup.php');
    exit();
}
?>

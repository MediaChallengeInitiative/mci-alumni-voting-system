<?php
/**
 * Voter Login Handler
 * Secure authentication with secret code, device binding, and prepared statements
 */

// Error handling for production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

// Define constant for config access
define('VOTING_SYSTEM', true);
include 'includes/config.php';
include 'includes/conn.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

if (isset($_POST['login'])) {
    $voterInput = trim($_POST['voter']);
    $password = $_POST['password'];
    $secretCode = isset($_POST['secret_code']) ? trim($_POST['secret_code']) : '';
    $deviceFingerprint = generateDeviceFingerprint();
    $deviceInfo = getDeviceInfo();

    // Validate input
    if (empty($voterInput) || empty($password)) {
        $_SESSION['error'] = 'Please enter both Voter ID and Password';
        header('Location: index.php');
        exit();
    }

    // Validate secret code (REQUIRED)
    if (empty($secretCode)) {
        $_SESSION['error'] = 'Secret code is required. Please enter the secret code provided by Media Challenge Initiative.';
        header('Location: index.php');
        exit();
    }

    if (!validateSecretCode($secretCode)) {
        logError("Invalid secret code login attempt from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . " - Input: $voterInput", 'security');
        $_SESSION['error'] = 'Invalid secret code. Please contact Media Challenge Initiative for the correct code.';
        header('Location: index.php');
        exit();
    }

    // Check if this device has been used by another voter
    $existingDevice = null;
    $deviceCheck = $conn->prepare("SELECT dr.voter_id, v.voters_id, v.firstname, v.lastname
                                   FROM device_registry dr
                                   JOIN voters v ON dr.voter_id = v.id
                                   WHERE dr.device_fingerprint = ? AND dr.is_blocked = 0");
    if ($deviceCheck) {
        $deviceCheck->bind_param("s", $deviceFingerprint);
        $deviceCheck->execute();
        $deviceResult = $deviceCheck->get_result();
        $existingDevice = $deviceResult->fetch_assoc();
        $deviceCheck->close();
    }

    // Try to find voter by Voter ID first, then by firstname + lastname combination
    $row = null;

    // Method 1: Search by exact Voter ID
    $stmt = $conn->prepare("SELECT id, voters_id, password, firstname, lastname, device_fingerprint, has_voted, is_logged_in FROM voters WHERE voters_id = ? LIMIT 1");
    $stmt->bind_param("s", $voterInput);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    $stmt->close();

    // Method 2: If not found by Voter ID, try firstname + lastname (case insensitive)
    if (!$row) {
        // Check if input contains a space (likely "Firstname Lastname")
        if (strpos($voterInput, ' ') !== false) {
            $nameParts = explode(' ', $voterInput, 2);
            $firstName = trim($nameParts[0]);
            $lastName = isset($nameParts[1]) ? trim($nameParts[1]) : '';

            if (!empty($firstName) && !empty($lastName)) {
                $stmt = $conn->prepare("SELECT id, voters_id, password, firstname, lastname, device_fingerprint, has_voted, is_logged_in FROM voters WHERE LOWER(firstname) = LOWER(?) AND LOWER(lastname) = LOWER(?) LIMIT 1");
                $stmt->bind_param("ss", $firstName, $lastName);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                }
                $stmt->close();
            }
        }
    }

    // Method 3: Try searching by firstname or lastname alone
    if (!$row) {
        $stmt = $conn->prepare("SELECT id, voters_id, password, firstname, lastname, device_fingerprint, has_voted, is_logged_in FROM voters WHERE LOWER(firstname) = LOWER(?) OR LOWER(lastname) = LOWER(?) LIMIT 1");
        $stmt->bind_param("ss", $voterInput, $voterInput);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }
        $stmt->close();
    }

    if (!$row) {
        logError("Failed login attempt - Invalid Voter ID/Name: $voterInput from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown'), 'security');
        $_SESSION['error'] = 'Invalid Voter ID or Name. Please check and try again.<br><small>Hint: Your Voter ID format is like MCIAMBA25</small>';
        header('Location: index.php');
        exit();
    }

    // Check if device was used by a different voter
    if ($existingDevice && $existingDevice['voter_id'] != $row['id']) {
        logError("Device reuse attempt - Voter: $voterInput, Original voter: " . $existingDevice['voters_id'], 'security');
        $_SESSION['error'] = 'This device has already been used by another voter (' .
            htmlspecialchars($existingDevice['firstname']) . ' ' .
            htmlspecialchars(substr($existingDevice['lastname'], 0, 1)) . '...). ' .
            'Each device can only be used by one voter. Please use a different device.';
        header('Location: index.php');
        exit();
    }

    // Check if voter is already logged in from another session
    if ($row['is_logged_in'] == 1 && !empty($row['device_fingerprint']) && $row['device_fingerprint'] != $deviceFingerprint) {
        $_SESSION['error'] = 'Your account is already logged in on another device. Please use the same device you originally logged in from, or contact an administrator.';
        header('Location: index.php');
        exit();
    }

    // Verify password
    if (password_verify($password, $row['password'])) {
        // Generate unique session token
        $sessionToken = bin2hex(random_bytes(32));

        // Update voter with device binding and session info
        $updateStmt = $conn->prepare("UPDATE voters SET
            device_fingerprint = ?,
            device_info = ?,
            session_token = ?,
            is_logged_in = 1,
            login_time = NOW()
            WHERE id = ?");
        $updateStmt->bind_param("sssi", $deviceFingerprint, $deviceInfo, $sessionToken, $row['id']);
        $updateStmt->execute();
        $updateStmt->close();

        // Register device if not already registered
        $checkDevice = $conn->prepare("SELECT id FROM device_registry WHERE device_fingerprint = ?");
        if ($checkDevice) {
            $checkDevice->bind_param("s", $deviceFingerprint);
            $checkDevice->execute();
            $deviceExists = $checkDevice->get_result()->num_rows > 0;
            $checkDevice->close();

            if (!$deviceExists) {
                $registerDevice = $conn->prepare("INSERT INTO device_registry (device_fingerprint, device_info, voter_id) VALUES (?, ?, ?)");
                if ($registerDevice) {
                    $registerDevice->bind_param("ssi", $deviceFingerprint, $deviceInfo, $row['id']);
                    $registerDevice->execute();
                    $registerDevice->close();
                }
            }
        }

        // Create session record
        $sessionExpiry = date('Y-m-d H:i:s', strtotime('+2 hours'));
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $insertSession = $conn->prepare("INSERT INTO voter_sessions (voter_id, session_token, device_fingerprint, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?, ?)");
        if ($insertSession) {
            $insertSession->bind_param("isssss", $row['id'], $sessionToken, $deviceFingerprint, $ip, $userAgent, $sessionExpiry);
            $insertSession->execute();
            $insertSession->close();
        }

        // Log successful login
        logError("Successful login: " . $row['voters_id'] . " (" . $row['firstname'] . " " . $row['lastname'] . ") from IP: $ip", 'login');

        // Regenerate session ID for security
        session_regenerate_id(true);
        $_SESSION['voter'] = $row['id'];
        $_SESSION['session_token'] = $sessionToken;
        $_SESSION['device_fingerprint'] = $deviceFingerprint;

        header('Location: home.php');
        exit();
    } else {
        logError("Failed login attempt - Wrong password for: $voterInput (Voter ID: " . $row['voters_id'] . ") from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown'), 'security');
        $_SESSION['error'] = 'Incorrect password. Please try again.';
    }
} else {
    $_SESSION['error'] = 'Please enter your credentials to continue';
}

header('Location: index.php');
exit();
?>

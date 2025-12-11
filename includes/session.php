<?php
    // Performance: Start output buffering
    ob_start();

    // Session configuration for performance
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.cookie_httponly', 1);

    session_start();
    include 'includes/conn.php';

    /**
     * Generate device fingerprint for validation
     */
    function getDeviceFingerprint() {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        return hash('sha256', $userAgent . $ip . $acceptLanguage);
    }

    if(isset($_SESSION['voter'])){
        // Validate session token and device fingerprint
        $sessionToken = isset($_SESSION['session_token']) ? $_SESSION['session_token'] : '';
        $currentFingerprint = getDeviceFingerprint();

        // Use prepared statement for security and performance
        $stmt = $conn->prepare("SELECT id, voters_id, firstname, lastname, photo, session_token, device_fingerprint, has_voted, is_logged_in
                                FROM voters WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $_SESSION['voter']);
        $stmt->execute();
        $result = $stmt->get_result();
        $voter = $result->fetch_assoc();
        $stmt->close();

        if(!$voter){
            // Invalid session, redirect to login
            session_destroy();
            header('Location: index.php');
            exit();
        }

        // Validate session token matches
        if(!empty($voter['session_token']) && $sessionToken !== $voter['session_token']){
            // Session token mismatch - possible session hijacking
            session_destroy();
            header('Location: index.php?error=session_invalid');
            exit();
        }

        // Validate device fingerprint
        if(!empty($voter['device_fingerprint']) && $currentFingerprint !== $voter['device_fingerprint']){
            // Device fingerprint mismatch - different device
            session_destroy();
            header('Location: index.php?error=device_mismatch');
            exit();
        }

        // Check if voter has already voted - they shouldn't be able to access voting pages
        if($voter['has_voted'] == 1 && basename($_SERVER['PHP_SELF']) == 'home.php'){
            // Allow viewing their submitted ballot but mark session for display purposes
            $_SESSION['vote_completed'] = true;
        }
    }
    else{
        header('Location: index.php');
        exit();
    }
?>

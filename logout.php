<?php
    session_start();
    include 'includes/conn.php';

    // Clear voter login state in database
    if(isset($_SESSION['voter'])){
        $voterId = $_SESSION['voter'];

        // Update voter record to mark as logged out
        $stmt = $conn->prepare("UPDATE voters SET is_logged_in = 0, session_token = NULL WHERE id = ?");
        if($stmt){
            $stmt->bind_param("i", $voterId);
            $stmt->execute();
            $stmt->close();
        }

        // Invalidate any active sessions
        $invalidate = $conn->prepare("UPDATE voter_sessions SET is_active = 0 WHERE voter_id = ?");
        if($invalidate){
            $invalidate->bind_param("i", $voterId);
            $invalidate->execute();
            $invalidate->close();
        }
    }

    // Clear all session data
    $_SESSION = array();

    // Destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy session
    session_destroy();

    // Redirect to login page
    header('Location: index.php');
    exit();
?>

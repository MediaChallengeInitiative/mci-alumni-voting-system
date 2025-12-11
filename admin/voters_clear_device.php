<?php
    include 'includes/session.php';

    if(isset($_POST['clear_device'])){
        $id = intval($_POST['id']);

        // Clear device binding and session for this voter
        $stmt = $conn->prepare("UPDATE voters SET device_fingerprint = NULL, device_info = NULL, session_token = NULL, is_logged_in = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);

        if($stmt->execute()){
            // Also delete from device_registry if exists
            $stmt2 = $conn->prepare("DELETE FROM device_registry WHERE voter_id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $stmt2->close();

            // Delete active sessions
            $stmt3 = $conn->prepare("DELETE FROM voter_sessions WHERE voter_id = ?");
            $stmt3->bind_param("i", $id);
            $stmt3->execute();
            $stmt3->close();

            $_SESSION['success'] = 'Device binding cleared successfully. Voter can now login from a new device.';
        }
        else{
            $_SESSION['error'] = 'Error clearing device binding: ' . $conn->error;
        }
        $stmt->close();
    }
    else{
        $_SESSION['error'] = 'Invalid request';
    }

    header('location: voters.php');
    exit();
?>

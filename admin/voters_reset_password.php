<?php
    include 'includes/session.php';

    if(isset($_POST['reset_password'])){
        $id = intval($_POST['id']);

        // Reset password to default: AwardsNight2025
        $defaultPassword = 'AwardsNight2025';
        $password = password_hash($defaultPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $conn->prepare("UPDATE voters SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password, $id);

        if($stmt->execute()){
            $_SESSION['success'] = 'Password reset successfully to: <strong>AwardsNight2025</strong>';
        }
        else{
            $_SESSION['error'] = 'Error resetting password: ' . $conn->error;
        }
        $stmt->close();
    }
    else{
        $_SESSION['error'] = 'Invalid request';
    }

    header('location: voters.php');
    exit();
?>

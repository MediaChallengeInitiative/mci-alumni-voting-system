<?php
/**
 * Voter Delete Handler
 * Secure deletion with prepared statements
 */

include 'includes/session.php';

if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);

    if ($id <= 0) {
        $_SESSION['error'] = 'Invalid voter ID';
        header('Location: voters.php');
        exit();
    }

    // First, delete related votes
    $deleteVotes = $conn->prepare("DELETE FROM votes WHERE voters_id = ?");
    $deleteVotes->bind_param("i", $id);
    $deleteVotes->execute();
    $deleteVotes->close();

    // Delete device registry entries
    $deleteDevice = $conn->prepare("DELETE FROM device_registry WHERE voter_id = ?");
    if ($deleteDevice) {
        $deleteDevice->bind_param("i", $id);
        $deleteDevice->execute();
        $deleteDevice->close();
    }

    // Delete voter sessions
    $deleteSessions = $conn->prepare("DELETE FROM voter_sessions WHERE voter_id = ?");
    if ($deleteSessions) {
        $deleteSessions->bind_param("i", $id);
        $deleteSessions->execute();
        $deleteSessions->close();
    }

    // Delete the voter using prepared statement
    $stmt = $conn->prepare("DELETE FROM voters WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Voter deleted successfully';
    } else {
        $_SESSION['error'] = 'Error deleting voter: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Select item to delete first';
}

header('Location: voters.php');
exit();
?>

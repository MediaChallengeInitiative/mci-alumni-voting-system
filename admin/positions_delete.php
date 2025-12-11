<?php
/**
 * Position Delete Handler
 * Secure deletion with prepared statements
 */

include 'includes/session.php';

if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);

    if ($id <= 0) {
        $_SESSION['error'] = 'Invalid position ID';
        header('Location: positions.php');
        exit();
    }

    // First, delete related candidates
    $deleteCandidates = $conn->prepare("DELETE FROM candidates WHERE position_id = ?");
    $deleteCandidates->bind_param("i", $id);
    $deleteCandidates->execute();
    $deleteCandidates->close();

    // Delete related votes
    $deleteVotes = $conn->prepare("DELETE FROM votes WHERE position_id = ?");
    $deleteVotes->bind_param("i", $id);
    $deleteVotes->execute();
    $deleteVotes->close();

    // Delete the position using prepared statement
    $stmt = $conn->prepare("DELETE FROM positions WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Position deleted successfully';
    } else {
        $_SESSION['error'] = 'Error deleting position: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Select item to delete first';
}

header('Location: positions.php');
exit();
?>

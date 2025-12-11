<?php
/**
 * Candidate Delete Handler
 * Secure deletion with prepared statements
 */

include 'includes/session.php';

if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);

    if ($id <= 0) {
        $_SESSION['error'] = 'Invalid candidate ID';
        header('Location: candidates.php');
        exit();
    }

    // Delete related votes first
    $deleteVotes = $conn->prepare("DELETE FROM votes WHERE candidate_id = ?");
    $deleteVotes->bind_param("i", $id);
    $deleteVotes->execute();
    $deleteVotes->close();

    // Delete the candidate using prepared statement
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Candidate deleted successfully';
    } else {
        $_SESSION['error'] = 'Error deleting candidate: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Select item to delete first';
}

header('Location: candidates.php');
exit();
?>

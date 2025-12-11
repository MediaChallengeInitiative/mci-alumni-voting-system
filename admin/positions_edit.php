<?php
/**
 * Position Edit Handler
 * Secure update with prepared statements
 */

include 'includes/session.php';

if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $description = trim($_POST['description']);
    $max_vote = intval($_POST['max_vote']);

    if ($id <= 0) {
        $_SESSION['error'] = 'Invalid position ID';
        header('Location: positions.php');
        exit();
    }

    if (empty($description)) {
        $_SESSION['error'] = 'Position description is required';
        header('Location: positions.php');
        exit();
    }

    if ($max_vote < 1) {
        $max_vote = 1;
    }

    // Use prepared statement for security
    $stmt = $conn->prepare("UPDATE positions SET description = ?, max_vote = ? WHERE id = ?");
    $stmt->bind_param("sii", $description, $max_vote, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Position updated successfully';
    } else {
        $_SESSION['error'] = 'Error updating position: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Fill up edit form first';
}

header('Location: positions.php');
exit();
?>

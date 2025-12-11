<?php
/**
 * Candidate Edit Handler
 * Secure update with prepared statements
 */

include 'includes/session.php';

if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $position = intval($_POST['position']);
    $platform = trim($_POST['platform']);

    if ($id <= 0) {
        $_SESSION['error'] = 'Invalid candidate ID';
        header('Location: candidates.php');
        exit();
    }

    if (empty($firstname) || empty($lastname)) {
        $_SESSION['error'] = 'First name and last name are required';
        header('Location: candidates.php');
        exit();
    }

    if ($position <= 0) {
        $_SESSION['error'] = 'Please select a valid position';
        header('Location: candidates.php');
        exit();
    }

    // Use prepared statement for security
    $stmt = $conn->prepare("UPDATE candidates SET firstname = ?, lastname = ?, position_id = ?, platform = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $firstname, $lastname, $position, $platform, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Candidate updated successfully';
    } else {
        $_SESSION['error'] = 'Error updating candidate: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Fill up edit form first';
}

header('Location: candidates.php');
exit();
?>

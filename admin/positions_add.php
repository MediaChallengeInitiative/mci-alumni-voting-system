<?php
/**
 * Position Add Handler
 * Secure insertion with prepared statements
 */

include 'includes/session.php';

if (isset($_POST['add'])) {
    $description = trim($_POST['description']);
    $max_vote = intval($_POST['max_vote']);

    if (empty($description)) {
        $_SESSION['error'] = 'Position description is required';
        header('Location: positions.php');
        exit();
    }

    if ($max_vote < 1) {
        $max_vote = 1;
    }

    // Get the next priority value
    $priorityQuery = $conn->query("SELECT MAX(priority) as max_priority FROM positions");
    $priorityRow = $priorityQuery->fetch_assoc();
    $priority = ($priorityRow['max_priority'] ?? 0) + 1;

    // Use prepared statement for security
    $stmt = $conn->prepare("INSERT INTO positions (description, max_vote, priority) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $description, $max_vote, $priority);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Position added successfully';
    } else {
        $_SESSION['error'] = 'Error adding position: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Fill up add form first';
}

header('Location: positions.php');
exit();
?>

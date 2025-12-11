<?php
/**
 * Candidate Add Handler
 * Secure insertion with prepared statements
 */

include 'includes/session.php';

if (isset($_POST['add'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $position = intval($_POST['position']);
    $platform = trim($_POST['platform']);

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

    // Handle file upload securely
    $filename = '';
    if (!empty($_FILES['photo']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            $_SESSION['error'] = 'Only JPG, PNG, and GIF images are allowed';
            header('Location: candidates.php');
            exit();
        }

        if ($_FILES['photo']['size'] > $max_size) {
            $_SESSION['error'] = 'Image size must be less than 5MB';
            header('Location: candidates.php');
            exit();
        }

        // Generate unique filename
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $ext;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], '../images/' . $filename)) {
            $_SESSION['error'] = 'Failed to upload image';
            header('Location: candidates.php');
            exit();
        }
    }

    // Use prepared statement for security
    $stmt = $conn->prepare("INSERT INTO candidates (position_id, firstname, lastname, photo, platform) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $position, $firstname, $lastname, $filename, $platform);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Candidate added successfully';
    } else {
        $_SESSION['error'] = 'Error adding candidate: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Fill up add form first';
}

header('Location: candidates.php');
exit();
?>

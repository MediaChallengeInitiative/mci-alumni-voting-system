<?php
/**
 * Admin Profile Update Handler
 * Secure update with prepared statements
 */

include 'includes/session.php';

// Determine return page (whitelist allowed pages)
$allowedPages = ['home.php', 'voters.php', 'positions.php', 'candidates.php', 'votes.php', 'ballot.php'];
$return = 'home.php';
if (isset($_GET['return']) && in_array($_GET['return'], $allowedPages)) {
    $return = $_GET['return'];
}

if (isset($_POST['save'])) {
    $curr_password = $_POST['curr_password'];
    $username = trim($_POST['username']);
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);

    // Validate required fields
    if (empty($username) || empty($firstname) || empty($lastname)) {
        $_SESSION['error'] = 'Username, firstname and lastname are required';
        header('Location: ' . $return);
        exit();
    }

    // Verify current password
    if (!password_verify($curr_password, $user['password'])) {
        $_SESSION['error'] = 'Incorrect current password';
        header('Location: ' . $return);
        exit();
    }

    // Handle file upload securely
    $filename = $user['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            $_SESSION['error'] = 'Only JPG, PNG, and GIF images are allowed';
            header('Location: ' . $return);
            exit();
        }

        if ($_FILES['photo']['size'] > $max_size) {
            $_SESSION['error'] = 'Image size must be less than 5MB';
            header('Location: ' . $return);
            exit();
        }

        // Generate unique filename
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_admin_' . uniqid() . '.' . $ext;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], '../images/' . $filename)) {
            $_SESSION['error'] = 'Failed to upload image';
            header('Location: ' . $return);
            exit();
        }
    }

    // Handle password update
    $password = $user['password']; // Keep current password by default
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'New password must be at least 6 characters';
            header('Location: ' . $return);
            exit();
        }
        $password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // Use prepared statement for security
    $stmt = $conn->prepare("UPDATE admin SET username = ?, password = ?, firstname = ?, lastname = ?, photo = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $username, $password, $firstname, $lastname, $filename, $user['id']);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Admin profile updated successfully';
    } else {
        $_SESSION['error'] = 'Error updating profile: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Fill up required details first';
}

header('Location: ' . $return);
exit();
?>

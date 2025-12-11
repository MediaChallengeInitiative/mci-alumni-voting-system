<?php
    include 'includes/session.php';

    if(isset($_POST['edit'])){
        $id = intval($_POST['id']);
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);

        // Use prepared statement to update voter
        $stmt = $conn->prepare("UPDATE voters SET firstname = ?, lastname = ? WHERE id = ?");
        $stmt->bind_param("ssi", $firstname, $lastname, $id);

        if($stmt->execute()){
            $_SESSION['success'] = 'Voter updated successfully';
        }
        else{
            $_SESSION['error'] = 'Error updating voter: ' . $conn->error;
        }
        $stmt->close();
    }
    else{
        $_SESSION['error'] = 'Please fill out the edit form first';
    }

    header('location: voters.php');
    exit();
?>

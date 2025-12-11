<?php
    include 'includes/session.php';

    if(isset($_POST['add'])){
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);

        // Auto-generate Voter ID in format: MCIA{FirstLetterOfFirstName}{FirstTwoLettersOfLastName}25
        // Example: Emmanuel Bahindi → MCIAEBA25 (all uppercase)
        $firstLetter = strtoupper(substr($firstname, 0, 1));
        $firstTwoOfLast = strtoupper(substr($lastname, 0, 2));

        $baseVoterId = "MCIA" . $firstLetter . $firstTwoOfLast . "25";

        // Check for duplicates and add numeric suffix if needed
        $voter_id = $baseVoterId;
        $counter = 1;

        $checkStmt = $conn->prepare("SELECT id FROM voters WHERE voters_id = ?");
        $checkStmt->bind_param("s", $voter_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        while($checkResult->num_rows > 0) {
            $counter++;
            $voter_id = $baseVoterId . $counter;
            $checkStmt->bind_param("s", $voter_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
        }
        $checkStmt->close();

        // Default password: AwardsNight2025 (hashed with bcrypt)
        $defaultPassword = 'AwardsNight2025';
        $password = password_hash($defaultPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        // Handle photo upload
        $filename = '';
        if(!empty($_FILES['photo']['name'])){
            $filename = time() . '_' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);
        }

        // Use prepared statement for security
        $stmt = $conn->prepare("INSERT INTO voters (voters_id, password, firstname, lastname, photo, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $voter_id, $password, $firstname, $lastname, $filename);

        if($stmt->execute()){
            $_SESSION['success'] = 'Voter added successfully!<br><strong>Voter ID:</strong> ' . htmlspecialchars($voter_id) . '<br><strong>Default Password:</strong> AwardsNight2025';
        }
        else{
            $_SESSION['error'] = 'Error adding voter: ' . $conn->error;
        }
        $stmt->close();
    }
    else{
        $_SESSION['error'] = 'Please fill out the registration form first';
    }

    header('location: voters.php');
    exit();
?>

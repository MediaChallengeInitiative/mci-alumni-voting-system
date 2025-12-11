<?php
    include 'includes/session.php';
    include 'includes/slugify.php';

    if(isset($_POST['vote'])){
        // Check if voter has already voted
        $checkVoted = $conn->prepare("SELECT has_voted FROM voters WHERE id = ?");
        $checkVoted->bind_param("i", $voter['id']);
        $checkVoted->execute();
        $votedResult = $checkVoted->get_result()->fetch_assoc();
        $checkVoted->close();

        if($votedResult['has_voted'] == 1){
            $_SESSION['error'][] = 'You have already submitted your vote. Each voter can only vote once.';
            header('location: home.php');
            exit();
        }

        if(count($_POST) == 1){
            $_SESSION['error'][] = 'Please vote for at least one candidate';
        }
        else{
            $_SESSION['post'] = $_POST;
            $sql = "SELECT * FROM positions";
            $query = $conn->query($sql);
            $error = false;
            $sql_array = array();

            while($row = $query->fetch_assoc()){
                $position = slugify($row['description']);
                $pos_id = $row['id'];

                if(isset($_POST[$position])){
                    if($row['max_vote'] > 1){
                        if(count($_POST[$position]) > $row['max_vote']){
                            $error = true;
                            $_SESSION['error'][] = 'You can only choose '.$row['max_vote'].' candidates for '.$row['description'];
                        }
                        else{
                            foreach($_POST[$position] as $values){
                                // Use prepared statement values
                                $sql_array[] = array(
                                    'voter_id' => $voter['id'],
                                    'candidate_id' => intval($values),
                                    'position_id' => $pos_id
                                );
                            }
                        }
                    }
                    else{
                        $candidate = intval($_POST[$position]);
                        $sql_array[] = array(
                            'voter_id' => $voter['id'],
                            'candidate_id' => $candidate,
                            'position_id' => $pos_id
                        );
                    }
                }
            }

            if(!$error && count($sql_array) > 0){
                // Begin transaction for data integrity
                $conn->begin_transaction();

                try {
                    // Insert all votes using prepared statement
                    $insertStmt = $conn->prepare("INSERT INTO votes (voters_id, candidate_id, position_id) VALUES (?, ?, ?)");

                    foreach($sql_array as $vote){
                        $insertStmt->bind_param("iii", $vote['voter_id'], $vote['candidate_id'], $vote['position_id']);
                        $insertStmt->execute();
                    }
                    $insertStmt->close();

                    // Mark voter as has_voted and log out
                    $updateVoter = $conn->prepare("UPDATE voters SET
                        has_voted = 1,
                        voted_at = NOW(),
                        is_logged_in = 0,
                        session_token = NULL
                        WHERE id = ?");
                    $updateVoter->bind_param("i", $voter['id']);
                    $updateVoter->execute();
                    $updateVoter->close();

                    // Invalidate voter sessions
                    $invalidateSessions = $conn->prepare("UPDATE voter_sessions SET is_active = 0 WHERE voter_id = ?");
                    if ($invalidateSessions) {
                        $invalidateSessions->bind_param("i", $voter['id']);
                        $invalidateSessions->execute();
                        $invalidateSessions->close();
                    }

                    // Commit transaction
                    $conn->commit();

                    unset($_SESSION['post']);

                    // Set success flag for confetti animation
                    $_SESSION['vote_success'] = true;
                    $_SESSION['success'] = 'Congratulations! Your vote has been successfully recorded. Thank you for participating in the 2025 Media Challenge Awards!';

                    // Redirect to success page (will auto-logout after showing success)
                    header('location: vote_success.php');
                    exit();

                } catch (Exception $e) {
                    // Rollback on error
                    $conn->rollback();
                    $_SESSION['error'][] = 'An error occurred while submitting your vote. Please try again.';
                }
            }
            elseif(count($sql_array) == 0 && !$error){
                $_SESSION['error'][] = 'Please select at least one candidate to vote';
            }
        }
    }
    else{
        $_SESSION['error'][] = 'Select candidates to vote first';
    }

    header('location: home.php');
    exit();
?>

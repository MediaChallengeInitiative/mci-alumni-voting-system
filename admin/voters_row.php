<?php
    include 'includes/session.php';

    if(isset($_POST['id'])){
        $id = intval($_POST['id']);

        $stmt = $conn->prepare("SELECT * FROM voters WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        echo json_encode($row);
    }
?>

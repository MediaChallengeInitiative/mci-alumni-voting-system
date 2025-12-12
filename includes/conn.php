<?php
	$conn = new mysqli('localhost', 'root', '', 'alumni_voting_db');

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	// Enable persistent connections and optimize settings
	$conn->set_charset("utf8mb4");
?>

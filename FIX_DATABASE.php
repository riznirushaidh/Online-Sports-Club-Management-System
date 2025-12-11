<?php
include 'db_connect.php';

// This is the command to fix your 'password_hash' column
$sql = "ALTER TABLE Members MODIFY COLUMN password_hash VARCHAR(255) NOT NULL";

if ($conn->query($sql) === TRUE) {
    echo "<h1>SUCCESS!</h1>";
    echo "<p>Your 'Members' table has been fixed.</p>";
    echo "<p>The 'password_hash' column can now store the full password.</p>";
    echo "<a href='CREATE_ADMIN.php'>Click here to create your admin account</a>";
} else {
    echo "<h1>Error:</h1>";
    echo "<p>Could not fix the table: " . $conn->error . "</p>";
}

$conn->close();
?>
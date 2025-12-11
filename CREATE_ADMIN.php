<?php
include 'db_connect.php';

// The password we want to use
$password_to_hash = 'admin123';

// Let PHP create a brand new, secure hash.
// This is the correct way.
$new_hash = password_hash($password_to_hash, PASSWORD_DEFAULT);

$username = 'admin';
$email = 'admin@club.com';
$full_name = 'Admin User';
$role = 'admin';

// First, delete any old, broken 'admin' user
$conn->query("DELETE FROM Members WHERE username = 'admin'");

// Now, insert the new, correct admin user with the new hash
$stmt = $conn->prepare("INSERT INTO Members (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $new_hash, $full_name, $role);

if ($stmt->execute()) {
    echo "<h1>SUCCESS!</h1>";
    echo "<p>The user 'admin' has been created.</p>";
    echo "<p>The new, correct hash is: <b>" . $new_hash . "</b></p>";
    echo "<a href='login.php'>Click here to log in</a>";
} else {
    echo "<h1>Error:</h1>";
    echo "<p>Could not create admin user: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
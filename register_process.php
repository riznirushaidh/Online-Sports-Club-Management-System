<?php
include 'db_connect.php';

// Get form data
$full_name = $_POST['full_name'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// 1. DATA VALIDATION (Required by your project)
$stmt_check = $conn->prepare("SELECT * FROM Members WHERE username = ? OR email = ?");
$stmt_check->bind_param("ss", $username, $email);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Meaningful user interaction
    echo "Error: Username or Email already exists. <a href='register.php'>Go back</a>";
} else {
    // 2. SECURE PASSWORD
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 3. CRUD (CREATE) Operation
    $stmt_insert = $conn->prepare("INSERT INTO Members (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("ssss", $full_name, $username, $email, $password_hash);

    if ($stmt_insert->execute()) {
        echo "Registration successful! <a href='login.php'>Click here to login</a>.";
    } else {
        echo "Error: " . $stmt_insert->error;
    }
    $stmt_insert->close();
}
$stmt_check->close();
$conn->close();
?>
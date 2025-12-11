<?php
session_start();
include 'db_connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

// 1. CRUD (READ) Operation
$stmt = $conn->prepare("SELECT * FROM Members WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    
    // 2. Verify the hashed password
    if (password_verify($password, $user['password_hash'])) {
        // SUCCESS!
        // 3. Store user info in the session to "log them in"
        $_SESSION['member_id'] = $user['member_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect to the correct dashboard
        if ($user['role'] == 'admin') {
            header("Location: admin_events.php");
        } else {
            header("Location: member_events.php");
        }
        exit;
    }
}

// Failed login
echo "Error: Invalid username or password. <a href='login.php'>Go back</a>";
$stmt->close();
$conn->close();
?>
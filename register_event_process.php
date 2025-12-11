<?php
session_start();
include 'db_connect.php';

// 1. SECURITY CHECK: Must be logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Check if event_id was provided in the URL
if (isset($_GET['event_id'])) {
    $member_id = $_SESSION['member_id'];
    $event_id = $_GET['event_id'];

    // 3. Data Validation: Check if they are ALREADY registered (prevents double-booking)
    $check_sql = "SELECT * FROM EventRegistrations WHERE member_id = ? AND event_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ii", $member_id, $event_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows == 0) {
        // 4. CRUD (CREATE): They are not registered, so insert them
        $insert_sql = "INSERT INTO EventRegistrations (member_id, event_id) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("ii", $member_id, $event_id);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    
    $stmt_check->close();
    $conn->close();
}

// 5. Send them back to the events page
header("Location: member_events.php");
exit;
?>
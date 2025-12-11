<?php
session_start();
include 'db_connect.php';

// 1. SECURITY CHECK: Must be logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Check if equipment_id was provided
if (isset($_GET['equipment_id'])) {
    $member_id = $_SESSION['member_id'];
    $equipment_id = $_GET['equipment_id'];

    // We must use a "Transaction"
    // This ensures BOTH queries succeed, or BOTH fail.
    // This prevents "lost" equipment.
    $conn->begin_transaction();

    try {
        // 3. Check availability AND lock the row for updating
        $check_sql = "SELECT * FROM Equipment WHERE equipment_id = ? AND available_quantity > 0 FOR UPDATE";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("i", $equipment_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows == 1) {
            // Item is available!
            
            // 4. UPDATE Equipment: Decrease available quantity by 1
            $update_sql = "UPDATE Equipment SET available_quantity = available_quantity - 1 WHERE equipment_id = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("i", $equipment_id);
            $stmt_update->execute();
            $stmt_update->close();
            
            // 5. INSERT into Borrowing: Log the checkout
            // Set deadline 7 days from now
            $borrow_date = date("Y-m-d H:i:s");
            $return_deadline = date("Y-m-d H:i:s", strtotime("+7 days"));
            
            $insert_sql = "INSERT INTO EquipmentBorrowing (member_id, equipment_id, borrow_date, return_deadline) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("iiss", $member_id, $equipment_id, $borrow_date, $return_deadline);
            $stmt_insert->execute();
            $stmt_insert->close();

            // 6. If both queries worked, commit the transaction
            $conn->commit();
            
        } else {
            // Item is not available, roll back
            $conn->rollback();
        }
        
        $stmt_check->close();

    } catch (mysqli_sql_exception $exception) {
        // An error occurred, roll back
        $conn->rollback();
        die("Error: Could not process borrow request.");
    }
}

// 7. Send them back to the equipment page
$conn->close();
header("Location: member_equipment.php");
exit;
?>
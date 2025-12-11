<?php
session_start();
include 'db_connect.php';

// 1. SECURITY CHECK: Must be logged in
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Check if borrow_id was provided
if (isset($_GET['borrow_id'])) {
    $member_id = $_SESSION['member_id'];
    $borrow_id = $_GET['borrow_id'];

    $conn->begin_transaction();
    try {
        // 3. Find the borrow record and the associated equipment_id
        $find_sql = "SELECT * FROM EquipmentBorrowing WHERE borrow_id = ? AND member_id = ? AND actual_return_date IS NULL FOR UPDATE";
        $stmt_find = $conn->prepare($find_sql);
        $stmt_find->bind_param("ii", $borrow_id, $member_id);
        $stmt_find->execute();
        $result = $stmt_find->get_result();

        if ($result->num_rows == 1) {
            $borrow_record = $result->fetch_assoc();
            $equipment_id = $borrow_record['equipment_id'];
            
            // 4. UPDATE Borrowing: Set the return date
            $update_borrow_sql = "UPDATE EquipmentBorrowing SET actual_return_date = NOW() WHERE borrow_id = ?";
            $stmt_update_borrow = $conn->prepare($update_borrow_sql);
            $stmt_update_borrow->bind_param("i", $borrow_id);
            $stmt_update_borrow->execute();
            $stmt_update_borrow->close();

            // 5. UPDATE Equipment: Increase available quantity by 1
            $update_equip_sql = "UPDATE Equipment SET available_quantity = available_quantity + 1 WHERE equipment_id = ?";
            $stmt_update_equip = $conn->prepare($update_equip_sql);
            $stmt_update_equip->bind_param("i", $equipment_id);
            $stmt_update_equip->execute();
            $stmt_update_equip->close();

            // 6. Commit
            $conn->commit();
        } else {
            // No record found, or already returned
            $conn->rollback();
        }
        $stmt_find->close();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        die("Error: Could not process return.");
    }
}

// 7. Send them back
$conn->close();
header("Location: member_equipment.php");
exit;
?>
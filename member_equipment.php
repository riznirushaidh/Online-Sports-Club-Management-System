<?php
include 'header.php'; // Includes session_start()
include 'db_connect.php';

// 1. SECURITY CHECK: Only allow members
if (!isset($_SESSION['member_id']) || $_SESSION['role'] != 'member') {
    header("Location: login.php");
    exit;
}

$member_id = $_SESSION['member_id'];
?>

<h3>Your Borrowed Items</h3>
<p>These are the items you currently have checked out. Please return them on time!</p>
<table class="table table-bordered mb-5">
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Date Borrowed</th>
            <th>Return Deadline</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // This SQL query joins EquipmentBorrowing with Equipment to get the item name
        // It only shows items for THIS member that have NOT been returned yet
        $borrow_sql = "SELECT b.borrow_id, e.item_name, b.borrow_date, b.return_deadline
                       FROM EquipmentBorrowing b
                       JOIN Equipment e ON b.equipment_id = e.equipment_id
                       WHERE b.member_id = ? AND b.actual_return_date IS NULL
                       ORDER BY b.return_deadline ASC";
        
        $stmt = $conn->prepare($borrow_sql);
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Check if the item is overdue
                $deadline = strtotime($row['return_deadline']);
                $is_overdue = $deadline < time();
                
                echo "<tr class='" . ($is_overdue ? "table-danger" : "") . "'>"; // Highlight if overdue
                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                echo "<td>" . date('M j, Y', strtotime($row['borrow_date'])) . "</td>";
                echo "<td>" . date('M j, Y', $deadline) . ($is_overdue ? " (Overdue)" : "") . "</td>";
                echo "<td><a href='return_equipment_process.php?borrow_id=" . $row['borrow_id'] . "' class='btn btn-sm btn-success'>Return</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>You have no items checked out.</td></tr>";
        }
        $stmt->close();
        ?>
    </tbody>
</table>


<h3>Available Equipment</h3>
<p>Select an item to borrow for 7 days.</p>
<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Item Name</th>
            <th>Available Quantity</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Only show items that have at least 1 available
        $sql = "SELECT * FROM Equipment WHERE available_quantity > 0 ORDER BY item_name";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                echo "<td>" . $row['available_quantity'] . "</td>";
                echo "<td>
                        <a href='borrow_equipment_process.php?equipment_id=" . $row['equipment_id'] . "' class='btn btn-sm btn-primary'>Borrow</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3' class='text-center'>No equipment is currently available.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$conn->close();
include 'footer.php'; 
?>
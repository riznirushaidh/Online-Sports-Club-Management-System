<?php
include 'header.php'; // Includes session_start()
include 'db_connect.php';

// 1. SECURITY CHECK: Only allow admins
if (!isset($_SESSION['member_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>

<h2>Admin Reports</h2>
<p>This page shows data on club activity.</p>

<div class="card mb-4">
    <div class="card-header">
        <h3>Event Attendance Report</h3>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Event Name</th>
                    <th>Event Date</th>
                    <th>Total Attendees</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // This SQL query joins Events with EventRegistrations
                // It counts (COUNT) registrations and groups them by event
                $report1_sql = "SELECT e.event_name, e.event_date, COUNT(r.member_id) as attendees 
                                FROM Events e
                                LEFT JOIN EventRegistrations r ON e.event_id = r.event_id
                                GROUP BY e.event_id
                                ORDER BY attendees DESC";
                
                $result1 = $conn->query($report1_sql);
                if ($result1->num_rows > 0) {
                    while ($row = $result1->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['event_name']) . "</td>";
                        echo "<td>" . date('M j, Y', strtotime($row['event_date'])) . "</td>";
                        echo "<td>" . $row['attendees'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No event data found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h3>Equipment Usage Report</h3>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Item Name</th>
                    <th>Total Times Borrowed</th>
                    <th>Currently Checked Out</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // This query joins Equipment with EquipmentBorrowing
                $report2_sql = "SELECT 
                                    e.item_name,
                                    COUNT(b.borrow_id) as times_borrowed,
                                    (e.total_quantity - e.available_quantity) as currently_out
                                FROM Equipment e
                                LEFT JOIN EquipmentBorrowing b ON e.equipment_id = b.equipment_id
                                GROUP BY e.equipment_id
                                ORDER BY times_borrowed DESC";
                
                $result2 = $conn->query($report2_sql);
                if ($result2->num_rows > 0) {
                    while ($row = $result2->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                        echo "<td>" . $row['times_borrowed'] . "</td>";
                        echo "<td>" . $row['currently_out'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No equipment data found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
include 'footer.php'; 
?>
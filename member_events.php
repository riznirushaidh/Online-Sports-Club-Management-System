<?php
include 'header.php'; // Includes session_start()
include 'db_connect.php';

// 1. SECURITY CHECK: Only allow members (or admins)
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

$member_id = $_SESSION['member_id'];

// Get a list of event IDs this member is already registered for
$registrations_sql = "SELECT event_id FROM EventRegistrations WHERE member_id = ?";
$stmt = $conn->prepare($registrations_sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$registered_events = [];
while ($row = $result->fetch_assoc()) {
    $registered_events[] = $row['event_id'];
}
$stmt->close();
?>

<h3>Upcoming Events</h3>
<p>Here are all the upcoming events. Click "Register" to sign up!</p>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Event Name</th>
            <th>Date</th>
            <th>Location</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Only show events that haven't happened yet
        $sql = "SELECT * FROM Events WHERE event_date >= NOW() ORDER BY event_date ASC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['event_name']) . "</td>";
                echo "<td>" . date('M j, Y, g:i A', strtotime($row['event_date'])) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>";
                
                // Check if the member's ID is in the array of registered events
                if (in_array($row['event_id'], $registered_events)) {
                    // If yes, show a "Registered" button
                    echo "<button class='btn btn-sm btn-success' disabled>Registered</button>";
                } else {
                    // If no, show the "Register" button
                    echo "<a href='register_event_process.php?event_id=" . $row['event_id'] . "' class='btn btn-sm btn-primary'>Register</a>";
                }
                
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='text-center'>No upcoming events found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$conn->close();
include 'footer.php'; 
?>
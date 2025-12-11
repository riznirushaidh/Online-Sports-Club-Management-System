<?php
include 'header.php'; // Includes session_start() and db_connect.php
include 'db_connect.php';

// 1. SECURITY CHECK: Only allow admins
if (!isset($_SESSION['member_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// --- LOGIC ---
$edit_mode = false;
$edit_event = null;

// CREATE or UPDATE
if (isset($_POST['save_event'])) {
    $name = $_POST['event_name'];
    $desc = $_POST['description'];
    $date = $_POST['event_date'];
    $loc = $_POST['location'];
    
    if (isset($_POST['event_id']) && !empty($_POST['event_id'])) {
        // UPDATE
        $id = $_POST['event_id'];
        $stmt = $conn->prepare("UPDATE Events SET event_name=?, description=?, event_date=?, location=? WHERE event_id=?");
        $stmt->bind_param("ssssi", $name, $desc, $date, $loc, $id);
    } else {
        // CREATE
        $stmt = $conn->prepare("INSERT INTO Events (event_name, description, event_date, location) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $desc, $date, $loc);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: admin_events.php"); // Refresh page
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM Events WHERE event_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_events.php"); // Refresh page
    exit;
}

// GET (for editing)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM Events WHERE event_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_event = $result->fetch_assoc();
    $stmt->close();
}

?>

<h3><?php echo $edit_mode ? 'Edit Event' : 'Create New Event'; ?></h3>
<form action="admin_events.php" method="POST" class="mb-5 p-4 bg-light rounded">
    <input type="hidden" name="event_id" value="<?php echo $edit_mode ? $edit_event['event_id'] : ''; ?>">
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Event Name</label>
            <input type="text" class="form-control" name="event_name" value="<?php echo $edit_mode ? $edit_event['event_name'] : ''; ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Event Date</label>
            <input type="datetime-local" class="form-control" name="event_date" value="<?php echo $edit_mode ? date('Y-m-d\TH:i', strtotime($edit_event['event_date'])) : ''; ?>" required>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" class="form-control" name="location" value="<?php echo $edit_mode ? $edit_event['location'] : ''; ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description"><?php echo $edit_mode ? $edit_event['description'] : ''; ?></textarea>
    </div>
    <button type="submit" name="save_event" class="btn btn-primary"><?php echo $edit_mode ? 'Update Event' : 'Create Event'; ?></button>
    <?php if ($edit_mode): ?>
        <a href="admin_events.php" class="btn btn-secondary">Cancel Edit</a>
    <?php endif; ?>
</form>

<h3>Current Events</h3>
<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Location</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM Events ORDER BY event_date DESC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['event_name']) . "</td>";
                echo "<td>" . date('M j, Y, g:i A', strtotime($row['event_date'])) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>
                        <a href='admin_events.php?edit=" . $row['event_id'] . "' class='btn btn-sm btn-warning'>Edit</a>
                        <a href='admin_events.php?delete=" . $row['event_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>No events found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$conn->close();
include 'footer.php'; 
?>
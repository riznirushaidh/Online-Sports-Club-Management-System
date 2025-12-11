<?php
include 'header.php'; // Includes session_start()
include 'db_connect.php';

// 1. SECURITY CHECK: Only allow admins
if (!isset($_SESSION['member_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// --- LOGIC ---

// DELETE Member
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Safety check: Do not let admin delete themselves (ID 1)
    if ($id == 1) {
        echo "<div class='alert alert-danger'>You cannot delete the primary admin account.</div>";
    } else {
        $stmt = $conn->prepare("DELETE FROM Members WHERE member_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_members.php"); // Refresh page
        exit;
    }
}
?>

<h2>Manage Members</h2>
<p>Here you can see all registered members in the system.</p>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Member ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Registered On</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM Members ORDER BY registration_date DESC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['member_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                echo "<td>" . date('M j, Y', strtotime($row['registration_date'])) . "</td>";
                echo "<td>";
                
                // Don't show a delete button for the admin (ID 1)
                if ($row['member_id'] != 1) {
                    echo "<a href='admin_members.php?delete=" . $row['member_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                }
                
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7' class='text-center'>No members found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$conn->close();
include 'footer.php'; 
?>
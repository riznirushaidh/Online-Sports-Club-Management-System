<?php
include 'header.php'; // Includes session_start()
include 'db_connect.php';

// 1. SECURITY CHECK: Only allow admins
if (!isset($_SESSION['member_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// --- LOGIC ---
$edit_mode = false;
$edit_item = null;

// CREATE or UPDATE
if (isset($_POST['save_item'])) {
    $item_name = $_POST['item_name'];
    $total_qty = $_POST['total_quantity'];
    $available_qty = $_POST['available_quantity'];
    
    if (isset($_POST['equipment_id']) && !empty($_POST['equipment_id'])) {
        // UPDATE
        $id = $_POST['equipment_id'];
        $stmt = $conn->prepare("UPDATE Equipment SET item_name=?, total_quantity=?, available_quantity=? WHERE equipment_id=?");
        $stmt->bind_param("siii", $item_name, $total_qty, $available_qty, $id);
    } else {
        // CREATE
        $stmt = $conn->prepare("INSERT INTO Equipment (item_name, total_quantity, available_quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $item_name, $total_qty, $available_qty);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: admin_equipment.php"); // Refresh page
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM Equipment WHERE equipment_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_equipment.php"); // Refresh page
    exit;
}

// GET (for editing)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM Equipment WHERE equipment_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_item = $result->fetch_assoc();
    $stmt->close();
}
?>

<h3><?php echo $edit_mode ? 'Edit Equipment' : 'Add New Equipment'; ?></h3>
<form action="admin_equipment.php" method="POST" class="mb-5 p-4 bg-light rounded">
    <input type="hidden" name="equipment_id" value="<?php echo $edit_mode ? $edit_item['equipment_id'] : ''; ?>">
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Item Name</label>
            <input type="text" class="form-control" name="item_name" value="<?php echo $edit_mode ? $edit_item['item_name'] : ''; ?>" required>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Total Quantity</label>
            <input type="number" class="form-control" name="total_quantity" value="<?php echo $edit_mode ? $edit_item['total_quantity'] : ''; ?>" required>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Available Quantity</label>
            <input type="number" class="form-control" name="available_quantity" value="<?php echo $edit_mode ? $edit_item['available_quantity'] : ''; ?>" required>
        </div>
    </div>
    <button type="submit" name="save_item" class="btn btn-primary"><?php echo $edit_mode ? 'Update Item' : 'Add Item'; ?></button>
    <?php if ($edit_mode): ?>
        <a href="admin_equipment.php" class="btn btn-secondary">Cancel Edit</a>
    <?php endif; ?>
</form>

<h3>Current Equipment Inventory</h3>
<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>Item Name</th>
            <th>Available</th>
            <th>Total</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM Equipment ORDER BY item_name";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                echo "<td>" . $row['available_quantity'] . "</td>";
                echo "<td>" . $row['total_quantity'] . "</td>";
                echo "<td>
                        <a href='admin_equipment.php?edit=" . $row['equipment_id'] . "' class='btn btn-sm btn-warning'>Edit</a>
                        <a href='admin_equipment.php?delete=" . $row['equipment_id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>No equipment found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$conn->close();
include 'footer.php'; 
?>
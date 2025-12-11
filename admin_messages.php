<?php
include 'header.php'; // Includes session_start()
include 'db_connect.php';

// 1. SECURITY CHECK: Only allow admins
if (!isset($_SESSION['member_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['member_id']; // Your admin ID
$selected_member_id = null;
$member_list = [];

// Get a list of all members who have sent a message (or received one from admin)
$members_sql = "SELECT DISTINCT m.member_id, m.username, m.full_name
                FROM Members m
                JOIN Messages msg ON (msg.sender_id = m.member_id OR msg.receiver_id = m.member_id)
                WHERE m.role = 'member' AND (msg.sender_id = $admin_id OR msg.receiver_id = $admin_id)";
$member_result = $conn->query($members_sql);
while ($row = $member_result->fetch_assoc()) {
    $member_list[] = $row;
}

// Check if a member is selected from the URL
if (isset($_GET['member_id'])) {
    $selected_member_id = intval($_GET['member_id']);
}

// --- LOGIC: Handle Sending a Reply ---
if (isset($_POST['send_reply']) && $selected_member_id) {
    $reply_content = $_POST['reply_content'];
    
    if (!empty($reply_content)) {
        // Create the message from the admin (sender) to the member (receiver)
        $stmt = $conn->prepare("INSERT INTO Messages (sender_id, receiver_id, message_content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $admin_id, $selected_member_id, $reply_content);
        $stmt->execute();
        $stmt->close();
        
        // Refresh the page to show the new message
        header("Location: admin_messages.php?member_id=" . $selected_member_id);
        exit;
    }
}
?>

<h2>Admin - Manage Messages</h2>
<p>Select a member from the list to view your conversation and send a reply.</p>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Conversations
            </div>
            <ul class="list-group list-group-flush">
                <?php
                if (count($member_list) > 0) {
                    foreach ($member_list as $member) {
                        $active_class = ($member['member_id'] == $selected_member_id) ? 'active' : '';
                        echo "<a href='admin_messages.php?member_id=" . $member['member_id'] . "' class='list-group-item list-group-item-action " . $active_class . "'>";
                        echo htmlspecialchars($member['full_name']) . " (" . htmlspecialchars($member['username']) . ")";
                        echo "</a>";
                    }
                } else {
                    echo "<li class='list-group-item'>No messages from members yet.</li>";
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Conversation
            </div>
            <div class="card-body" style="height: 400px; overflow-y: scroll;">
                <?php
                // If a member is selected, show the chat history
                if ($selected_member_id) {
                    $sql = "SELECT m.*, s.username as sender_username
                            FROM Messages m
                            JOIN Members s ON m.sender_id = s.member_id
                            WHERE (m.sender_id = ? AND m.receiver_id = ?)
                               OR (m.sender_id = ? AND m.receiver_id = ?)
                            ORDER BY m.timestamp ASC";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiii", $admin_id, $selected_member_id, $selected_member_id, $admin_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Check if the message was sent by the admin
                            $is_sender = ($row['sender_id'] == $admin_id);
                            
                            echo "<div class='mb-2 " . ($is_sender ? "text-end" : "text-start") . "'>";
                            echo "<small class='fw-bold'>" . htmlspecialchars($row['sender_username']) . "</small><br>";
                            echo "<div class='d-inline-block p-2 rounded " . ($is_sender ? "bg-primary text-white" : "bg-light") . "'>";
                            echo htmlspecialchars($row['message_content']);
                            echo "</div>";
                            echo "<div class='text-muted' style='font-size: 0.75rem;'>" . date('M j, g:i A', strtotime($row['timestamp'])) . "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p class='text-center'>No messages with this member yet.</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p class='text-center'>Please select a member from the list to view messages.</p>";
                }
                ?>
            </div>
            
            <?php if ($selected_member_id): // Only show reply form if a chat is active ?>
            <div class="card-footer">
                <form action="admin_messages.php?member_id=<?php echo $selected_member_id; ?>" method="POST">
                    <div class="input-group">
                        <input type="text" class="form-control" name="reply_content" placeholder="Type your reply..." required>
                        <button type="submit" name="send_reply" class.="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'footer.php'; 
?>
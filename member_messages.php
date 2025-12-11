<?php
include 'header.php'; // Includes session_start()
include 'db_connect.php';

// 1. SECURITY CHECK: Only allow members
if (!isset($_SESSION['member_id']) || $_SESSION['role'] != 'member') {
    header("Location: login.php");
    exit;
}

$member_id = $_SESSION['member_id'];
$admin_id = 9; // We assume the admin's ID is 9 (from our SQL insert)

// --- LOGIC: Handle Sending a Message ---
if (isset($_POST['send_message'])) {
    $message_content = $_POST['message_content'];
    
    if (!empty($message_content)) {
        // Create the message from the member (sender) to the admin (receiver)
        $stmt = $conn->prepare("INSERT INTO Messages (sender_id, receiver_id, message_content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $member_id, $admin_id, $message_content);
        $stmt->execute();
        $stmt->close();
        
        // Refresh the page to show the new message
        header("Location: member_messages.php");
        exit;
    }
}
?>

<h2>Messages to Admin</h2>
<p>You can send inquiries or confirmation requests to the admin here.</p>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Your Conversation with Admin
            </div>
            <div class="card-body" style="height: 400px; overflow-y: scroll;">
                <?php
                // This query gets all messages sent BY the member TO the admin
                // OR sent BY the admin TO the member.
                $sql = "SELECT m.*, s.username as sender_username
                        FROM Messages m
                        JOIN Members s ON m.sender_id = s.member_id
                        WHERE (m.sender_id = ? AND m.receiver_id = ?)
                           OR (m.sender_id = ? AND m.receiver_id = ?)
                        ORDER BY m.timestamp ASC";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiii", $member_id, $admin_id, $admin_id, $member_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Check if the message was sent by the logged-in member
                        $is_sender = ($row['sender_id'] == $member_id);
                        
                        echo "<div class='mb-2 " . ($is_sender ? "text-end" : "text-start") . "'>";
                        echo "<small class='fw-bold'>" . htmlspecialchars($row['sender_username']) . "</small><br>";
                        echo "<div class='d-inline-block p-2 rounded " . ($is_sender ? "bg-primary text-white" : "bg-light") . "'>";
                        echo htmlspecialchars($row['message_content']);
                        echo "</div>";
                        echo "<div class='text-muted' style='font-size: 0.75rem;'>" . date('M j, g:i A', strtotime($row['timestamp'])) . "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='text-center'>No messages yet. Send one to start the conversation!</p>";
                }
                $stmt->close();
                ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Send New Message
            </div>
            <div class="card-body">
                <form action="member_messages.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">To:</label>
                        <input type="text" class="form-control" value="Admin" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="message_content">Your Message:</label>
                        <textarea class="form-control" name="message_content" id="message_content" rows="5" required></textarea>
                    </div>
                    <button type="submit" name="send_message" class="btn btn-primary w-100">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'footer.php'; 
?>
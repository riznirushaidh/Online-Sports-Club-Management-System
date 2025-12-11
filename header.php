<?php
// Start a session on every page
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Club Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Sports Club</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['member_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="#">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</a></li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="admin_events.php">Manage Events</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_equipment.php">Manage Equipment</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_members.php">Manage Members</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_messages.php">Manage Messages</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_reports.php">View Reports</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="member_events.php">View/Book Events</a></li>
                <li class="nav-item"><a class="nav-link" href="member_equipment.php">Borrow Equipment</a></li>
                <li class="nav-item"><a class="nav-link" href="member_messages.php">Messages</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
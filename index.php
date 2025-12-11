<?php include 'header.php'; // Our beautiful header ?>

<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Welcome to the Sports Club!</h1>
        <p class="col-md-8 fs-4">
            This is the new online management system. You can register for an account, 
            book events, and borrow equipment all in one place.
        </p>

        <?php if (isset($_SESSION['member_id'])): ?>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="admin_events.php" class="btn btn-primary btn-lg">Go to Admin Dashboard</a>
            <?php else: ?>
                <a href="member_events.php" class="btn btn-primary btn-lg">View Upcoming Events</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary btn-lg">Login</a>
            <a href="register.php" class="btn btn-secondary btn-lg">Register</a>
        <?php endif; ?>
        
    </div>
</div>

<?php include 'footer.php'; // Our footer ?>
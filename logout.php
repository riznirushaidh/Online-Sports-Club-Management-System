<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php"); // Send back to login page
exit;
?>
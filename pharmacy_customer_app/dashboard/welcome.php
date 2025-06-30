<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
<p>You are logged in.</p>
<a href="logout.php">Logout</a>

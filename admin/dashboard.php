<?php
// admin/dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
$name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
    <nav>
        <a href="../logout.php">Logout</a>
    </nav>
    <h3>Admin Dashboard</h3>
    <ul>
        <li>User Management</li>
        <li>Platform Monitoring</li>
        <li>Backups & System Settings</li>
    </ul>
    <p>(Admin features coming soon...)</p>
</body>
</html>

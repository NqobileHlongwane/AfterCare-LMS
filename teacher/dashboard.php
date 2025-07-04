<?php
// teacher/dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../login.php');
    exit();
}
$name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
    <nav>
        <a href="groups.php">Group Management</a> |
        <a href="assignments.php">Task Creation</a> |
        <a href="submissions.php">Submission Review</a> |
        <a href="meetings.php">Meeting Link Uploader</a> |
        <a href="dashboard.php">Dashboard</a> |
        <a href="../logout.php">Logout</a>
    </nav>
    <h3>Your Dashboard</h3>
    <ul>
        <li><a href="groups.php">Group Management</a></li>
        <li><a href="assignments.php">Task Creation</a></li>
        <li><a href="submissions.php">Submission Review</a></li>
        <li><a href="meetings.php">Meeting Link Uploader</a></li>
        <li><a href="announcements.php">Announcements</a></li>
        <li>Learner Progress</li>
    </ul>
</body>
</html>

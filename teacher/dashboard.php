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
    <style>
        .navbar-logo {
            position: absolute;
            left: 24px;
            top: 50%;
            transform: translateY(-50%);
            height: 48px;
            display: flex;
            align-items: center;
            background: #fff;
            padding: 4px 12px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(30,41,59,0.10);
        }
        .navbar-logo img {
            max-height: 48px;
            width: auto;
        }
        .navbar {
            background: #1e293b;
            color: #fff;
            padding: 16px 0 16px 0;
            text-align: center;
            font-size: 1.3rem;
            letter-spacing: 1px;
            margin-bottom: 40px;
            box-shadow: 0 2px 8px rgba(30,41,59,0.08);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="../logo/aftercare-logo.jpg" alt="AfterCare LMS Logo">
        </div>
        Teacher Dashboard
    </div>
    <h2 style="text-align:center;">Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
    <nav style="text-align:center;">
        <a href="groups.php">Group Management</a> |
        <a href="assignments.php">Task Creation</a> |
        <a href="submissions.php">Submission Review</a> |
        <a href="meetings.php">Meeting Link Uploader</a> |
        <a href="dashboard.php">Dashboard</a> |
        <a href="../logout.php">Logout</a>
    </nav>
    <h3 style="text-align:center;">Your Dashboard</h3>
    <ul>
        <li><a href="groups.php">Group Management</a></li>
        <li><a href="assignments.php">Task Creation</a></li>
        <li><a href="submissions.php">Submission Review</a></li>
        <li><a href="meetings.php">Meeting Link Uploader</a></li>
        <li><a href="announcements.php">Announcements</a></li>
        <li>Learner Progress</li>
    </ul>
</body>
<footer>
    <p>&copy; 2025 Nqobile Hlongwane. All rights reserved.</p>
  </footer>
</html>

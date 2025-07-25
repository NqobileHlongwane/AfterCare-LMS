<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header('Location: login.php');
    exit();
}
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Learner';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learner Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(120deg, #e0e7ff 0%, #f8fafc 100%);
            margin: 0;
            min-height: 100vh;
        }
        .navbar {
            background: #1e293b;
            color: #fff;
            padding: 16px 0;
            text-align: center;
            font-size: 1.3rem;
            letter-spacing: 1px;
            margin-bottom: 40px;
            box-shadow: 0 2px 8px rgba(30,41,59,0.08);
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(30,41,59,0.10);
            padding: 32px 28px 24px 28px;
        }
        h2 {
            color: #1e293b;
            margin-bottom: 8px;
            text-align: center;
            font-weight: 700;
        }
        .logout-link {
            text-align: right;
            margin-bottom: 10px;
        }
        .logout-link a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
        }
        .logout-link a:hover {
            text-decoration: underline;
        }
        h3 {
            color: #334155;
            margin-bottom: 18px;
            text-align: center;
        }
        .dashboard-list {
            list-style: none;
            padding: 0;
            margin: 0 0 18px 0;
        }
        .dashboard-list li {
            background: #f1f5f9;
            margin-bottom: 12px;
            padding: 14px 18px;
            border-radius: 7px;
            color: #334155;
            font-size: 1.08rem;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px rgba(30,41,59,0.04);
        }
        .dashboard-list li:before {
            content: '\2022';
            color: #6366f1;
            font-size: 1.5em;
            margin-right: 12px;
        }
        .coming-soon {
            text-align: center;
            color: #64748b;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="navbar">
        Online Learning Management System
    </div>
    <div class="container">
        <div class="logout-link">
            <a href="logout.php">Logout</a>
        </div>
        <h2>Welcome, <?php echo htmlspecialchars($name); ?></h2>
        <h3>Your Dashboard</h3>
        <ul class="dashboard-list">
            <li onclick="location.href='learner/assigned_tasks.php'" style="cursor:pointer;"><a href="learner/assigned_tasks.php" style="color:#6366f1;text-decoration:none;font-weight:500;width:100%;display:block;">Assigned Tasks/Assignments</a></li>
            <li onclick="location.href='learner/submit_assignment.php'" style="cursor:pointer;"><a href="learner/submit_assignment.php" style="color:#6366f1;text-decoration:none;font-weight:500;width:100%;display:block;">Submit Assignment</a></li>
            <li onclick="location.href='learner/meetings.php'" style="cursor:pointer;"><a href="learner/meetings.php" style="color:#6366f1;text-decoration:none;font-weight:500;width:100%;display:block;">Upcoming Meetings</a></li>
            <li onclick="location.href='learner/announcements.php'" style="cursor:pointer;"><a href="learner/announcements.php" style="color:#6366f1;text-decoration:none;font-weight:500;width:100%;display:block;">Announcements</a></li>
            <li onclick="location.href='learner/grades.php'" style="cursor:pointer;"><a href="learner/grades.php" style="color:#6366f1;text-decoration:none;font-weight:500;width:100%;display:block;">Grades/Results</a></li>
            <li onclick="location.href='learner/groups.php'" style="cursor:pointer;"><a href="learner/groups.php" style="color:#6366f1;text-decoration:none;font-weight:500;width:100%;display:block;">Groups</a></li>
            <li onclick="location.href='learner/notifications.php'" style="cursor:pointer;"><a href="learner/notifications.php" style="color:#6366f1;text-decoration:none;font-weight:500;width:100%;display:block;">Notifications</a></li>
        </ul>
        <div class="coming-soon">(Dashboard features coming soon...)</div>
    </div>
</body>
<footer>
    <p>&copy; 2025 Nqobile Hlongwane. All rights reserved.</p>
  </footer>
</html>

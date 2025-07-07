<?php
// learner/dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header('Location: ../login.php');
    exit();
}
$name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learner Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: linear-gradient(120deg, #e0e7ff 0%, #f8fafc 100%); margin: 0; min-height: 100vh; }
        .navbar {
            background: #1e293b;
            color: #fff;
            padding: 16px 0;
            font-size: 1.3rem;
            letter-spacing: 1px;
            margin-bottom: 40px;
            box-shadow: 0 2px 8px rgba(30,41,59,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .navbar-logo {
            display: flex;
            align-items: center;
            height: 40px;
            position: absolute;
            left: 24px;
            top: 50%;
            transform: translateY(-50%);
            margin: 0;
        }
        .navbar-logo img {
            max-height: 36px;
            width: auto;
            background: #fff;
            padding: 2px 8px;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(30,41,59,0.10);
        }
        .navbar-title {
            font-weight: 700;
            color: #fff;
            margin: 0 auto;
        }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,41,59,0.10); padding: 32px 28px 24px 28px; }
        .logout-link { text-align: right; margin-bottom: 10px; }
        .logout-link a { color: #6366f1; text-decoration: none; font-weight: 500; }
        .logout-link a:hover { text-decoration: underline; }
        .welcome { text-align: center; font-size: 1.2rem; color: #334155; margin-bottom: 18px; }
        h2 { color: #1e293b; margin-bottom: 8px; text-align: center; font-weight: 700; }
        h3 { color: #334155; margin-bottom: 18px; text-align: center; }
        .dashboard-list { list-style: none; padding: 0; margin: 0 0 18px 0; }
        .dashboard-list li { background: #f1f5f9; margin-bottom: 12px; padding: 14px 18px; border-radius: 7px; color: #334155; font-size: 1.08rem; display: flex; align-items: center; box-shadow: 0 1px 3px rgba(30,41,59,0.04); }
        .dashboard-list li:before { content: '\2022'; color: #6366f1; font-size: 1.5em; margin-right: 12px; }
        .dashboard-list li a { color: #6366f1; text-decoration: none; font-weight: 500; width: 100%; display: block; }
        .dashboard-list li:hover { background: #e0e7ff; cursor: pointer; }
        .coming-soon { text-align: center; color: #64748b; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="/LMS/logo/aftercare-logo.jpg" alt="AfterCare LMS Logo">
        </div>
        <span class="navbar-title">Online Learning Management System</span>
    </div>
    <div class="container">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($name); ?></div>
        <div class="logout-link">
            <a href="../logout.php">Logout</a>
        </div>
        <h3>Your Dashboard</h3>
        <ul class="dashboard-list">
            <li onclick="location.href='assigned_tasks.php'"><a href="assigned_tasks.php">Assigned Tasks/Assignments</a></li>
            <li onclick="location.href='submit_assignment.php'"><a href="submit_assignment.php">Submit Assignment</a></li>
            <li onclick="location.href='meetings.php'"><a href="meetings.php">Upcoming Meetings</a></li>
            <li onclick="location.href='announcements.php'"><a href="announcements.php">Announcements</a></li>
            <li onclick="location.href='grades.php'"><a href="grades.php">Grades/Results</a></li>
            <li onclick="location.href='groups.php'"><a href="groups.php">Groups</a></li>
            <li onclick="location.href='notifications.php'"><a href="notifications.php">Notifications</a></li>
        </ul>
        <div class="coming-soon">(Dashboard features coming soon...)</div>
    </div>
</body>
</html>

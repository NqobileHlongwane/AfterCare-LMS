<?php
// learner/groups.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header('Location: ../login.php');
    exit();
}
require '../config.php';
$learner_id = $_SESSION['user_id'];

// Get groups for this learner
$sql = "SELECT g.id, g.name, u.name AS teacher_name
        FROM group_members gm
        JOIN groups g ON gm.group_id = g.id
        JOIN users u ON g.teacher_id = u.id
        WHERE gm.learner_id = $learner_id";
$result = $conn->query($sql);
$groups = [];
while ($row = $result->fetch_assoc()) {
    $groups[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Groups - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: #f8fafc; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,41,59,0.10); padding: 32px 28px 24px 28px; }
        h2 { color: #1e293b; text-align: center; font-weight: 700; }
        .navbar { background: #1e293b; color: #fff; padding: 16px 0; text-align: center; font-size: 1.3rem; letter-spacing: 1px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(30,41,59,0.08); }
        .back-link { margin-bottom: 18px; display: block; color: #6366f1; text-decoration: none; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
        .group-list { margin-top: 24px; }
        .group-item { background: #f1f5f9; border-radius: 8px; padding: 16px 18px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(30,41,59,0.04); }
        .group-title { font-size: 1.08rem; font-weight: 700; color: #334155; }
        .group-meta { color: #64748b; font-size: 0.98rem; margin-top: 4px; }
        .no-groups { color: #64748b; text-align: center; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="navbar">Online Learning Management System</div>
    <div class="container">
        <a class="back-link" href="dashboard.php">&larr; Back to Dashboard</a>
        <h2>Your Groups</h2>
        <div class="group-list">
            <?php if ($groups): ?>
                <?php foreach ($groups as $group): ?>
                    <div class="group-item">
                        <div class="group-title"><?php echo htmlspecialchars($group['name']); ?></div>
                        <div class="group-meta">Teacher: <?php echo htmlspecialchars($group['teacher_name']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-groups">You are not assigned to any group yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

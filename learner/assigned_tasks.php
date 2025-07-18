<?php
// learner/assigned_tasks.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header('Location: ../login.php');
    exit();
}
require '../config.php';
$learner_id = $_SESSION['user_id'];

// Get group IDs for this learner
$group_ids = [];
$result = $conn->query("SELECT group_id FROM group_members WHERE learner_id = $learner_id");
while ($row = $result->fetch_assoc()) {
    $group_ids[] = $row['group_id'];
}
$tasks = [];
if ($group_ids) {
    $group_ids_str = implode(',', $group_ids);
    $sql = "SELECT a.id, a.title, a.description, a.due_date, a.file_path, g.name AS group_name
            FROM assignments a
            JOIN groups g ON a.group_id = g.id
            WHERE a.group_id IN ($group_ids_str)
            ORDER BY a.due_date ASC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assigned Tasks - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: #f8fafc; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,41,59,0.10); padding: 32px 28px 24px 28px; }
        h2 { color: #1e293b; text-align: center; font-weight: 700; }
        .navbar { background: #1e293b; color: #fff; padding: 16px 0; text-align: center; font-size: 1.3rem; letter-spacing: 1px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(30,41,59,0.08); }
        .task-list { margin-top: 24px; }
        .task-item { background: #f1f5f9; border-radius: 8px; padding: 18px 20px; margin-bottom: 18px; box-shadow: 0 1px 3px rgba(30,41,59,0.04); }
        .task-title { font-size: 1.1rem; font-weight: 700; color: #334155; }
        .task-meta { color: #64748b; font-size: 0.98rem; margin-bottom: 6px; }
        .task-desc { color: #334155; margin-top: 8px; }
        .no-tasks { color: #64748b; text-align: center; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="navbar">Online Learning Management System</div>
    <div class="container">
        <h2>Your Assigned Tasks</h2>
        <div class="task-list">
            <?php if ($tasks): ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task-item">
                        <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                        <div class="task-meta">Group: <?php echo htmlspecialchars($task['group_name']); ?> | Due: <?php echo htmlspecialchars($task['due_date']); ?></div>
                        <div class="task-desc"><?php echo nl2br(htmlspecialchars($task['description'])); ?></div>
                        <?php if (!empty($task['file_path'])): ?>
                            <div style="margin-top:8px;"><a href="../<?php echo htmlspecialchars($task['file_path']); ?>" target="_blank" style="color:#6366f1;font-weight:500;">Download Attachment</a></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-tasks">No tasks assigned to you yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
<footer>
    <p>&copy; 2025 Nqobile Hlongwane. All rights reserved.</p>
  </footer>
</html>

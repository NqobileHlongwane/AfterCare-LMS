<?php
// learner/announcements.php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header('Location: ../login.html');
    exit;
}

$learner_id = $_SESSION['user_id'];

// Get all group IDs the learner belongs to
$sql = "SELECT group_id FROM group_members WHERE learner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $learner_id);
$stmt->execute();
$result = $stmt->get_result();
$group_ids = [];
while ($row = $result->fetch_assoc()) {
    $group_ids[] = $row['group_id'];
}
$stmt->close();

$announcements = [];
if (!empty($group_ids)) {
    $in = str_repeat('?,', count($group_ids) - 1) . '?';
    $sql = "SELECT a.*, g.name as group_name, u.name as teacher_name FROM announcements a JOIN groups g ON a.group_id = g.id JOIN users u ON a.teacher_id = u.id WHERE a.group_id IN ($in) ORDER BY a.created_at DESC";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($group_ids));
    $params = [];
    $params[] = & $types;
    foreach ($group_ids as $k => $gid) {
        $params[] = & $group_ids[$k];
    }
    call_user_func_array([$stmt, 'bind_param'], $params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Announcements</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Group Announcements</h2>
        <?php if (empty($announcements)): ?>
            <p>No announcements available for your groups.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Teacher</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td><?= htmlspecialchars($announcement['group_name']) ?></td>
                            <td><?= htmlspecialchars($announcement['title']) ?></td>
                            <td><?= nl2br(htmlspecialchars($announcement['message'])) ?></td>
                            <td><?= htmlspecialchars($announcement['teacher_name']) ?></td>
                            <td><?= htmlspecialchars($announcement['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>

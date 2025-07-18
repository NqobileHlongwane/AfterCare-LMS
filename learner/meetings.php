<?php
// learner/meetings.php
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

$meetings = [];
if (!empty($group_ids)) {
    $in = str_repeat('?,', count($group_ids) - 1) . '?';
    $sql = "SELECT m.*, g.name as group_name FROM meetings m JOIN groups g ON m.group_id = g.id WHERE m.group_id IN ($in) ORDER BY m.date DESC";
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
        $meetings[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Meetings</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Group Meetings</h2>
        <?php if (empty($meetings)): ?>
            <p>No meeting links available for your groups.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Link</th>
                        <th>Date & Time</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($meetings as $meeting): ?>
                        <tr>
                            <td><?= htmlspecialchars($meeting['group_name']) ?></td>
                            <td><a href="<?= htmlspecialchars($meeting['link']) ?>" target="_blank">Join</a></td>
                            <td><?= htmlspecialchars($meeting['date']) ?></td>
                            <td><?= htmlspecialchars($meeting['description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
<footer>
    <p>&copy; 2025 Nqobile Hlongwane. All rights reserved.</p>
  </footer>
</html>

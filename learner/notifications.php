<?php
// learner/notifications.php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header('Location: ../login.html');
    exit;
}

$learner_id = $_SESSION['user_id'];

$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $learner_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();

// Mark all as read
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $learner_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Your Notifications</h2>
        <?php if (empty($notifications)): ?>
            <p>No notifications yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($notifications as $note): ?>
                    <li style="margin-bottom:10px;<?= $note['is_read'] ? '' : 'font-weight:bold;' ?>">
                        <?= htmlspecialchars($note['message']) ?>
                        <span style="color:#64748b;font-size:0.9em;">(<?= htmlspecialchars($note['created_at']) ?>)</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
<footer>
    <p>&copy; 2025 Nqobile Hlongwane. All rights reserved.</p>
  </footer>
</html>

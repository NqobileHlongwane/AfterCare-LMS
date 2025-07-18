<?php
// teacher/announcements.php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../login.html');
    exit;
}

$teacher_id = $_SESSION['user_id'];

// Handle announcement creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $message = $_POST['message'] ?? '';
    if ($group_id && $title && $message) {
        $stmt = $conn->prepare("INSERT INTO announcements (group_id, teacher_id, title, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param('iiss', $group_id, $teacher_id, $title, $message);
        $stmt->execute();
        $stmt->close();
        // Notify all learners in the group
        $stmt = $conn->prepare("SELECT learner_id FROM group_members WHERE group_id = ?");
        $stmt->bind_param('i', $group_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $notif_msg = "A new announcement ('$title') has been posted to your group.";
        while ($row = $result->fetch_assoc()) {
            $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt2->bind_param('is', $row['learner_id'], $notif_msg);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
        $success = 'Announcement posted successfully!';
    } else {
        $error = 'Please fill in all fields.';
    }
}

// Fetch groups for this teacher
$stmt = $conn->prepare("SELECT id, name FROM groups WHERE teacher_id = ?");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$groups = $stmt->get_result();
$stmt->close();

// Fetch announcements for this teacher's groups
$stmt = $conn->prepare("SELECT a.*, g.name as group_name FROM announcements a JOIN groups g ON a.group_id = g.id WHERE g.teacher_id = ? ORDER BY a.created_at DESC");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$announcements = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Announcements - Teacher</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Post Announcement</h2>
        <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="post" class="form">
            <label for="group_id">Group:</label>
            <select name="group_id" id="group_id" required>
                <option value="">Select Group</option>
                <?php while ($group = $groups->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($group['id']) ?>"><?= htmlspecialchars($group['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>
            <label for="message">Message:</label>
            <textarea name="message" id="message" required></textarea>
            <button type="submit">Post Announcement</button>
        </form>
        <h3>Existing Announcements</h3>
        <table>
            <thead>
                <tr>
                    <th>Group</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($announcement = $announcements->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($announcement['group_name']) ?></td>
                        <td><?= htmlspecialchars($announcement['title']) ?></td>
                        <td><?= nl2br(htmlspecialchars($announcement['message'])) ?></td>
                        <td><?= htmlspecialchars($announcement['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
<footer>
    <p>&copy; 2025 Nqobile Hlongwane. All rights reserved.</p>
  </footer>
</html>

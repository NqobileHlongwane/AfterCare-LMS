<?php
// teacher/meetings.php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../login.html');
    exit;
}

$teacher_id = $_SESSION['user_id'];

// Handle meeting creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group_id'] ?? '';
    $link = $_POST['link'] ?? '';
    $date = $_POST['date'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($group_id && $link && $date) {
        $stmt = $conn->prepare("INSERT INTO meetings (group_id, link, date, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $group_id, $link, $date, $description);
        $stmt->execute();
        $stmt->close();
        // Notify all learners in the group
        $stmt = $conn->prepare("SELECT learner_id FROM group_members WHERE group_id = ?");
        $stmt->bind_param('i', $group_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $notif_msg = "A new meeting link has been added to your group.";
        while ($row = $result->fetch_assoc()) {
            $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt2->bind_param('is', $row['learner_id'], $notif_msg);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
        $success = 'Meeting link uploaded successfully!';
    } else {
        $error = 'Please fill in all required fields.';
    }
}

// Fetch groups for this teacher
$stmt = $conn->prepare("SELECT id, name FROM groups WHERE teacher_id = ?");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$groups = $stmt->get_result();
$stmt->close();

// Fetch meetings for this teacher's groups
$stmt = $conn->prepare("SELECT m.*, g.name as group_name FROM meetings m JOIN groups g ON m.group_id = g.id WHERE g.teacher_id = ? ORDER BY m.date DESC");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$meetings = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Meeting Links - Teacher</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Upload Meeting Link</h2>
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
            <label for="link">Meeting Link (Zoom/Google Meet):</label>
            <input type="url" name="link" id="link" required>
            <label for="date">Date & Time:</label>
            <input type="datetime-local" name="date" id="date" required>
            <label for="description">Description (optional):</label>
            <textarea name="description" id="description"></textarea>
            <button type="submit">Upload Meeting Link</button>
        </form>
        <h3>Existing Meetings</h3>
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
                <?php while ($meeting = $meetings->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($meeting['group_name']) ?></td>
                        <td><a href="<?= htmlspecialchars($meeting['link']) ?>" target="_blank">Join</a></td>
                        <td><?= htmlspecialchars($meeting['date']) ?></td>
                        <td><?= htmlspecialchars($meeting['description']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="teacher_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
// teacher/assignments.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../login.php');
    exit();
}
require '../config.php';
$teacher_id = $_SESSION['user_id'];

// Fetch groups for this teacher
$stmt = $conn->prepare("SELECT id, name FROM groups WHERE teacher_id = ?");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$stmt->bind_result($gid, $gname);
$groups = [];
while ($stmt->fetch()) {
    $groups[] = ['id' => $gid, 'name' => $gname];
}
$stmt->close();

// Handle assignment upload, edit, and delete
$message = '';
// Delete assignment
if (isset($_GET['delete'])) {
    $assignment_id = intval($_GET['delete']);
    // Remove file if exists
    $res = $conn->query("SELECT file_path FROM assignments WHERE id = $assignment_id");
    if ($row = $res->fetch_assoc()) {
        if ($row['file_path'] && file_exists('../' . $row['file_path'])) {
            unlink('../' . $row['file_path']);
        }
    }
    $conn->query("DELETE FROM assignments WHERE id = $assignment_id");
    $message = 'Assignment deleted.';
}
// Edit assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_assignment'])) {
    $assignment_id = intval($_POST['assignment_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $file_path = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf','docx','pptx','jpg','png','zip'];
        $max_size = 10 * 1024 * 1024; // 10MB
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['file']['size'] <= $max_size) {
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = uniqid('assign_') . '.' . $ext;
            $file_path = $upload_dir . $file_name;
            move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
            $file_path = 'assets/uploads/' . $file_name;
        } else {
            $message = 'Invalid file type or size.';
        }
    }
    if ($title && $due_date && !$message) {
        if ($file_path) {
            $stmt = $conn->prepare("UPDATE assignments SET title=?, description=?, due_date=?, file_path=? WHERE id=?");
            $stmt->bind_param('ssssi', $title, $description, $due_date, $file_path, $assignment_id);
        } else {
            $stmt = $conn->prepare("UPDATE assignments SET title=?, description=?, due_date=? WHERE id=?");
            $stmt->bind_param('sssi', $title, $description, $due_date, $assignment_id);
        }
        $stmt->execute();
        $stmt->close();
        $message = 'Assignment updated!';
    } elseif (!$message) {
        $message = 'Please fill in all required fields.';
    }
}
// Create assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_assignment'])) {
    $group_id = intval($_POST['group_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $file_path = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf','docx','pptx','jpg','png','zip'];
        $max_size = 10 * 1024 * 1024; // 10MB
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['file']['size'] <= $max_size) {
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = uniqid('assign_') . '.' . $ext;
            $file_path = $upload_dir . $file_name;
            move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
            $file_path = 'assets/uploads/' . $file_name;
        } else {
            $message = 'Invalid file type or size.';
        }
    }
    if ($title && $group_id && $due_date && !$message) {
        $stmt = $conn->prepare("INSERT INTO assignments (group_id, title, description, file_path, due_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $group_id, $title, $description, $file_path, $due_date);
        $stmt->execute();
        $stmt->close();
        // Notify all learners in the group
        $stmt = $conn->prepare("SELECT learner_id FROM group_members WHERE group_id = ?");
        $stmt->bind_param('i', $group_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $notif_msg = $file_path ? "A new assignment/resource ('$title') has been uploaded to your group." : "A new assignment ('$title') has been uploaded to your group.";
        while ($row = $result->fetch_assoc()) {
            $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt2->bind_param('is', $row['learner_id'], $notif_msg);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
        $message = 'Assignment created successfully!';
    } elseif (!$message) {
        $message = 'Please fill in all required fields.';
    }
}
// Fetch assignments
$sql = "SELECT a.id, a.title, a.due_date, g.name AS group_name, a.file_path FROM assignments a JOIN groups g ON a.group_id = g.id WHERE g.teacher_id = $teacher_id ORDER BY a.due_date DESC";
$result = $conn->query($sql);
$assignments = [];
while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assignments - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: #f8fafc; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,41,59,0.10); padding: 32px 28px 24px 28px; }
        h2 { color: #1e293b; text-align: center; font-weight: 700; }
        .navbar { background: #1e293b; color: #fff; padding: 16px 0; text-align: center; font-size: 1.3rem; letter-spacing: 1px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(30,41,59,0.08); }
        .back-link { margin-bottom: 18px; display: block; color: #6366f1; text-decoration: none; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
        .form-section { background: #f1f5f9; border-radius: 8px; padding: 18px 20px; margin-bottom: 28px; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; color: #334155; font-weight: 500; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; background: #fff; transition: border 0.2s; }
        input:focus, select:focus, textarea:focus { border: 1.5px solid #6366f1; outline: none; }
        button { padding: 10px 24px; background: #6366f1; color: #fff; border: none; border-radius: 6px; font-size: 1.05rem; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #4f46e5; }
        .message { color: #16a34a; text-align: center; margin-bottom: 12px; font-weight: 500; }
        .error { color: #dc2626; text-align: center; margin-bottom: 12px; font-weight: 500; }
        .assignment-list { margin-top: 24px; }
        .assignment-item { background: #f1f5f9; border-radius: 8px; padding: 16px 18px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(30,41,59,0.04); }
        .assignment-title { font-size: 1.08rem; font-weight: 700; color: #334155; }
        .assignment-meta { color: #64748b; font-size: 0.98rem; margin-top: 4px; }
        .assignment-file { margin-top: 6px; }
        .no-assignments { color: #64748b; text-align: center; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="navbar">Online Learning Management System</div>
    <div class="container">
        <a class="back-link" href="teacher_dashboard.php">&larr; Back to Dashboard</a>
        <h2>Upload & Assign Tasks</h2>
        <?php if ($message): ?>
            <div class="<?php echo strpos($message, 'success') !== false ? 'message' : 'error'; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="form-section">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="group_id">Assign to Group</label>
                    <select name="group_id" id="group_id" required>
                        <option value="">Select group</option>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date" required>
                </div>
                <div class="form-group">
                    <label for="file">Attach File (optional, max 10MB, pdf/docx/pptx/jpg/png/zip)</label>
                    <input type="file" name="file" id="file" accept=".pdf,.docx,.pptx,.jpg,.png,.zip">
                </div>
                <button type="submit" name="create_assignment">Create Assignment</button>
            </form>
        </div>
        <h2>Existing Assignments</h2>
        <div class="assignment-list">
            <?php if ($assignments): ?>
                <?php foreach ($assignments as $a): ?>
                    <div class="assignment-item">
                        <div class="assignment-title"><?php echo htmlspecialchars($a['title']); ?></div>
                        <div class="assignment-meta">Group: <?php echo htmlspecialchars($a['group_name']); ?> | Due: <?php echo htmlspecialchars($a['due_date']); ?></div>
                        <?php if ($a['file_path']): ?>
                            <div class="assignment-file"><a href="../<?php echo htmlspecialchars($a['file_path']); ?>" target="_blank">Download Attachment</a></div>
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data" style="margin-top:10px;display:inline-block;" action="assignments.php">
                            <input type="hidden" name="assignment_id" value="<?php echo $a['id']; ?>">
                            <input type="text" name="title" value="<?php echo htmlspecialchars($a['title']); ?>" placeholder="Title" required style="width:120px;">
                            <input type="text" name="description" value="<?php echo htmlspecialchars($a['description'] ?? ''); ?>" placeholder="Description" required style="width:120px;">
                            <input type="date" name="due_date" value="<?php echo htmlspecialchars($a['due_date']); ?>" required>
                            <input type="file" name="file" accept=".pdf,.docx,.pptx,.jpg,.png,.zip">
                            <button type="submit" name="edit_assignment">Edit</button>
                            <a href="assignments.php?delete=<?php echo $a['id']; ?>" onclick="return confirm('Delete this assignment?')" style="color:#dc2626;margin-left:8px;">Delete</a>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-assignments">No assignments created yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

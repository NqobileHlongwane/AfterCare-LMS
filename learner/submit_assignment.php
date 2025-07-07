<?php
// learner/submit_assignment.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header('Location: ../login.php');
    exit();
}
require '../config.php';
$learner_id = $_SESSION['user_id'];

// Get assignments for this learner's groups
$group_ids = [];
$result = $conn->query("SELECT group_id FROM group_members WHERE learner_id = $learner_id");
while ($row = $result->fetch_assoc()) {
    $group_ids[] = $row['group_id'];
}
$assignments = [];
if ($group_ids) {
    $group_ids_str = implode(',', $group_ids);
    $sql = "SELECT a.id, a.title, a.due_date, g.name AS group_name
            FROM assignments a
            JOIN groups g ON a.group_id = g.id
            WHERE a.group_id IN ($group_ids_str)
            ORDER BY a.due_date DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
}
// Handle submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'])) {
    $assignment_id = intval($_POST['assignment_id']);
    $file_path = null;
    $status = 'submitted';
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf','docx','pptx','jpg','png','zip'];
        $max_size = 10 * 1024 * 1024; // 10MB
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['file']['size'] <= $max_size) {
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = uniqid('sub_') . '.' . $ext;
            $file_path = $upload_dir . $file_name;
            move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
            $file_path = 'assets/uploads/' . $file_name;
        } else {
            $message = 'Invalid file type or size.';
        }
    }
    if ($assignment_id && $file_path && !$message) {
        $stmt = $conn->prepare("INSERT INTO submissions (assignment_id, learner_id, file_path, status, submitted_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE file_path=VALUES(file_path), status=VALUES(status), submitted_at=NOW()");
        $stmt->bind_param('iiss', $assignment_id, $learner_id, $file_path, $status);
        $stmt->execute();
        $stmt->close();
        $message = 'Assignment submitted successfully!';
    } elseif (!$message) {
        $message = 'Please select an assignment and upload a file.';
    }
}
// Fetch previous submissions
$submissions = [];
$sql = "SELECT s.assignment_id, s.file_path, s.status, s.submitted_at, a.title FROM submissions s JOIN assignments a ON s.assignment_id = a.id WHERE s.learner_id = $learner_id ORDER BY s.submitted_at DESC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Assignment - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: #f8fafc; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,41,59,0.10); padding: 32px 28px 24px 28px; }
        h2 { color: #1e293b; text-align: center; font-weight: 700; }
        .navbar { background: #1e293b; color: #fff; padding: 16px 0; text-align: center; font-size: 1.3rem; letter-spacing: 1px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(30,41,59,0.08); }
        .form-section { background: #f1f5f9; border-radius: 8px; padding: 18px 20px; margin-bottom: 28px; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; color: #334155; font-weight: 500; }
        input, select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; background: #fff; transition: border 0.2s; }
        input:focus, select:focus { border: 1.5px solid #6366f1; outline: none; }
        button { padding: 10px 24px; background: #6366f1; color: #fff; border: none; border-radius: 6px; font-size: 1.05rem; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #4f46e5; }
        .message { color: #16a34a; text-align: center; margin-bottom: 12px; font-weight: 500; }
        .error { color: #dc2626; text-align: center; margin-bottom: 12px; font-weight: 500; }
        .submission-list { margin-top: 24px; }
        .submission-item { background: #f1f5f9; border-radius: 8px; padding: 16px 18px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(30,41,59,0.04); }
        .submission-title { font-size: 1.08rem; font-weight: 700; color: #334155; }
        .submission-meta { color: #64748b; font-size: 0.98rem; margin-top: 4px; }
        .submission-file { margin-top: 6px; }
        .no-submissions { color: #64748b; text-align: center; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="navbar">Online Learning Management System</div>
    <div class="container">
        <h2>Submit Assignment</h2>
        <?php if ($message): ?>
            <div class="<?php echo strpos($message, 'success') !== false ? 'message' : 'error'; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="form-section">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="assignment_id">Assignment</label>
                    <select name="assignment_id" id="assignment_id" required>
                        <option value="">Select assignment</option>
                        <?php foreach ($assignments as $a): ?>
                            <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['title']) . ' (Group: ' . htmlspecialchars($a['group_name']) . ', Due: ' . htmlspecialchars($a['due_date']) . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="file">Upload File (max 10MB, pdf/docx/pptx/jpg/png/zip)</label>
                    <input type="file" name="file" id="file" accept=".pdf,.docx,.pptx,.jpg,.png,.zip" required>
                </div>
                <button type="submit">Submit Assignment</button>
            </form>
        </div>
        <h2>Your Submissions</h2>
        <div class="submission-list">
            <?php if ($submissions): ?>
                <?php foreach ($submissions as $s): ?>
                    <div class="submission-item">
                        <div class="submission-title"><?php echo htmlspecialchars($s['title']); ?></div>
                        <div class="submission-meta">Status: <?php echo htmlspecialchars($s['status']); ?> | Submitted: <?php echo htmlspecialchars($s['submitted_at']); ?></div>
                        <?php if ($s['file_path']): ?>
                            <div class="submission-file"><a href="../<?php echo htmlspecialchars($s['file_path']); ?>" target="_blank">Download Submission</a></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-submissions">No submissions yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

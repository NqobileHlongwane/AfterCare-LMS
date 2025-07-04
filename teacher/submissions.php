<?php
// teacher/submissions.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../login.php');
    exit();
}
require '../config.php';
$teacher_id = $_SESSION['user_id'];

// Fetch assignments for this teacher
$sql = "SELECT a.id, a.title, g.name AS group_name FROM assignments a JOIN groups g ON a.group_id = g.id WHERE g.teacher_id = $teacher_id ORDER BY a.due_date DESC";
$result = $conn->query($sql);
$assignments = [];
while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}

// Get selected assignment
$selected_assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : (count($assignments) ? $assignments[0]['id'] : 0);

// Fetch submissions for the selected assignment
$submissions = [];
if ($selected_assignment_id) {
    $sql = "SELECT s.id, s.file_path, s.status, s.submitted_at, u.name AS learner_name, u.email
            FROM submissions s
            JOIN users u ON s.learner_id = u.id
            WHERE s.assignment_id = $selected_assignment_id
            ORDER BY s.submitted_at DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Submissions - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: #f8fafc; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,41,59,0.10); padding: 32px 28px 24px 28px; }
        h2 { color: #1e293b; text-align: center; font-weight: 700; }
        .navbar { background: #1e293b; color: #fff; padding: 16px 0; text-align: center; font-size: 1.3rem; letter-spacing: 1px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(30,41,59,0.08); }
        .back-link { margin-bottom: 18px; display: block; color: #6366f1; text-decoration: none; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
        .form-section { background: #f1f5f9; border-radius: 8px; padding: 18px 20px; margin-bottom: 28px; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; color: #334155; font-weight: 500; }
        select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; background: #fff; transition: border 0.2s; }
        select:focus { border: 1.5px solid #6366f1; outline: none; }
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
        <a class="back-link" href="teacher_dashboard.php">&larr; Back to Dashboard</a>
        <h2>View Learner Submissions</h2>
        <div class="form-section">
            <form method="get">
                <div class="form-group">
                    <label for="assignment_id">Select Assignment</label>
                    <select name="assignment_id" id="assignment_id" onchange="this.form.submit()">
                        <?php foreach ($assignments as $a): ?>
                            <option value="<?php echo $a['id']; ?>" <?php if ($a['id'] == $selected_assignment_id) echo 'selected'; ?>><?php echo htmlspecialchars($a['title']) . ' (Group: ' . htmlspecialchars($a['group_name']) . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
        <div class="submission-list">
            <?php if ($submissions): ?>
                <?php foreach ($submissions as $s): ?>
                    <div class="submission-item">
                        <div class="submission-title"><?php echo htmlspecialchars($s['learner_name']); ?> (<?php echo htmlspecialchars($s['email']); ?>)</div>
                        <div class="submission-meta">Status: <?php echo htmlspecialchars($s['status']); ?> | Submitted: <?php echo htmlspecialchars($s['submitted_at']); ?></div>
                        <?php if ($s['file_path']): ?>
                            <div class="submission-file"><a href="../<?php echo htmlspecialchars($s['file_path']); ?>" target="_blank">Download Submission</a></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-submissions">No submissions for this assignment yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

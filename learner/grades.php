<?php
// learner/grades.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    header('Location: ../login.php');
    exit();
}
require '../config.php';
$learner_id = $_SESSION['user_id'];

// Fetch grades and feedback for this learner
$sql = "SELECT a.title, g.name AS group_name, gr.mark, gr.comment, s.submitted_at
        FROM submissions s
        JOIN assignments a ON s.assignment_id = a.id
        JOIN groups g ON a.group_id = g.id
        LEFT JOIN grades gr ON s.id = gr.submission_id
        WHERE s.learner_id = $learner_id AND s.status = 'graded'
        ORDER BY s.submitted_at DESC";
$result = $conn->query($sql);
$grades = [];
while ($row = $result->fetch_assoc()) {
    $grades[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Grades & Feedback - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: #f8fafc; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,41,59,0.10); padding: 32px 28px 24px 28px; }
        h2 { color: #1e293b; text-align: center; font-weight: 700; }
        .navbar { background: #1e293b; color: #fff; padding: 16px 0; text-align: center; font-size: 1.3rem; letter-spacing: 1px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(30,41,59,0.08); }
        .back-link { margin-bottom: 18px; display: block; color: #6366f1; text-decoration: none; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
        .grade-list { margin-top: 24px; }
        .grade-item { background: #f1f5f9; border-radius: 8px; padding: 16px 18px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(30,41,59,0.04); }
        .grade-title { font-size: 1.08rem; font-weight: 700; color: #334155; }
        .grade-meta { color: #64748b; font-size: 0.98rem; margin-top: 4px; }
        .grade-mark { color: #16a34a; font-weight: 700; }
        .grade-feedback { margin-top: 8px; color: #334155; }
        .no-grades { color: #64748b; text-align: center; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="navbar">Online Learning Management System</div>
    <div class="container">
        <a class="back-link" href="dashboard.php">&larr; Back to Dashboard</a>
        <h2>Your Grades & Feedback</h2>
        <div class="grade-list">
            <?php if ($grades): ?>
                <?php foreach ($grades as $g): ?>
                    <div class="grade-item">
                        <div class="grade-title"><?php echo htmlspecialchars($g['title']); ?></div>
                        <div class="grade-meta">Group: <?php echo htmlspecialchars($g['group_name']); ?> | Submitted: <?php echo htmlspecialchars($g['submitted_at']); ?></div>
                        <div class="grade-mark">Grade: <?php echo ($g['mark'] !== null) ? htmlspecialchars($g['mark']) . '%' : 'N/A'; ?></div>
                        <div class="grade-feedback">Feedback: <?php echo htmlspecialchars($g['comment'] ?? ''); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-grades">No graded assignments yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

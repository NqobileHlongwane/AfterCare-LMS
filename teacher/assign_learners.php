<?php
// teacher/assign_learners.php
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

// Fetch all learners
$learners = [];
$result = $conn->query("SELECT id, name, email, grade FROM users WHERE role = 'learner'");
while ($row = $result->fetch_assoc()) {
    $learners[] = $row;
}

// Handle assignment
if (isset($_POST['group_id']) && isset($_POST['learner_ids'])) {
    $group_id = intval($_POST['group_id']);
    $learner_ids = $_POST['learner_ids'];
    // Remove all current members for this group
    $conn->query("DELETE FROM group_members WHERE group_id = $group_id");
    // Add selected learners
    $stmt = $conn->prepare("INSERT INTO group_members (group_id, learner_id) VALUES (?, ?)");
    foreach ($learner_ids as $lid) {
        $stmt->bind_param('ii', $group_id, $lid);
        $stmt->execute();
    }
    $stmt->close();
    header('Location: assign_learners.php?group_id=' . $group_id . '&success=1');
    exit();
}

// Get selected group and its members
$selected_group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : (count($groups) ? $groups[0]['id'] : 0);
$group_members = [];
if ($selected_group_id) {
    $result = $conn->query("SELECT learner_id FROM group_members WHERE group_id = $selected_group_id");
    while ($row = $result->fetch_assoc()) {
        $group_members[] = $row['learner_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Learners to Groups - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: #f8fafc; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,41,59,0.10); padding: 32px 28px 24px 28px; }
        h2 { color: #1e293b; text-align: center; font-weight: 700; }
        .navbar { background: #1e293b; color: #fff; padding: 16px 0; text-align: center; font-size: 1.3rem; letter-spacing: 1px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(30,41,59,0.08); }
        .back-link { margin-bottom: 18px; display: block; color: #6366f1; text-decoration: none; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
        .group-select { margin-bottom: 24px; }
        .learners-list { max-height: 320px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; background: #f1f5f9; }
        .learner-item { margin-bottom: 10px; display: flex; align-items: center; }
        .learner-item label { margin-left: 8px; color: #334155; }
        .submit-btn { margin-top: 18px; width: 100%; padding: 12px; background: #6366f1; color: #fff; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .submit-btn:hover { background: #4f46e5; }
        .success-msg { color: #16a34a; text-align: center; margin-bottom: 12px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="navbar">Online Learning Management System</div>
    <div class="container">
        <a class="back-link" href="teacher_dashboard.php">&larr; Back to Dashboard</a>
        <h2>Assign Learners to Groups</h2>
        <?php if (isset($_GET['success'])): ?>
            <div class="success-msg">Learner assignments updated!</div>
        <?php endif; ?>
        <form method="get" class="group-select">
            <label for="group_id">Select Group:</label>
            <select name="group_id" id="group_id" onchange="this.form.submit()">
                <?php foreach ($groups as $g): ?>
                    <option value="<?php echo $g['id']; ?>" <?php if ($g['id'] == $selected_group_id) echo 'selected'; ?>><?php echo htmlspecialchars($g['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php if ($selected_group_id): ?>
        <form method="post">
            <input type="hidden" name="group_id" value="<?php echo $selected_group_id; ?>">
            <div class="learners-list">
                <?php foreach ($learners as $learner): ?>
                    <div class="learner-item">
                        <input type="checkbox" name="learner_ids[]" value="<?php echo $learner['id']; ?>" id="learner_<?php echo $learner['id']; ?>" <?php if (in_array($learner['id'], $group_members)) echo 'checked'; ?>>
                        <label for="learner_<?php echo $learner['id']; ?>"><?php echo htmlspecialchars($learner['name']) . " (" . htmlspecialchars($learner['email']) . ", Grade: " . htmlspecialchars($learner['grade']) . ")"; ?></label>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($learners)): ?>
                    <div style="color:#64748b;">No learners found.</div>
                <?php endif; ?>
            </div>
            <button type="submit" class="submit-btn">Save Assignments</button>
        </form>
        <?php else: ?>
            <div style="color:#64748b;text-align:center;">No groups available. Please create a group first.</div>
        <?php endif; ?>
    </div>
</body>
</html>

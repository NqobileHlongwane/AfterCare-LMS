<?php
// teacher/groups.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../login.php');
    exit();
}
require '../config.php';
$teacher_id = $_SESSION['user_id'];

// Handle create group
if (isset($_POST['create_group'])) {
    $group_name = trim($_POST['group_name']);
    if ($group_name) {
        $stmt = $conn->prepare("INSERT INTO groups (name, teacher_id) VALUES (?, ?)");
        $stmt->bind_param('si', $group_name, $teacher_id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: groups.php');
    exit();
}
// Handle delete group
if (isset($_GET['delete'])) {
    $group_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM groups WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param('ii', $group_id, $teacher_id);
    $stmt->execute();
    $stmt->close();
    header('Location: groups.php');
    exit();
}
// Handle edit group
if (isset($_POST['edit_group'])) {
    $group_id = intval($_POST['group_id']);
    $group_name = trim($_POST['group_name']);
    if ($group_name) {
        $stmt = $conn->prepare("UPDATE groups SET name = ? WHERE id = ? AND teacher_id = ?");
        $stmt->bind_param('sii', $group_name, $group_id, $teacher_id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: groups.php');
    exit();
}
// Fetch groups
$stmt = $conn->prepare("SELECT id, name FROM groups WHERE teacher_id = ?");
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$stmt->bind_result($gid, $gname);
$groups = [];
while ($stmt->fetch()) {
    $groups[] = ['id' => $gid, 'name' => $gname];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Groups - LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: #f8fafc; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(30,41,59,0.10); padding: 32px 28px 24px 28px; }
        h2 { color: #1e293b; text-align: center; font-weight: 700; }
        .group-list { margin: 24px 0; }
        .group-item { display: flex; align-items: center; justify-content: space-between; background: #f1f5f9; padding: 12px 18px; border-radius: 7px; margin-bottom: 10px; }
        .group-actions button { margin-left: 8px; }
        form.inline { display: inline; }
        .navbar { background: #1e293b; color: #fff; padding: 16px 0; text-align: center; font-size: 1.3rem; letter-spacing: 1px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(30,41,59,0.08); }
        .back-link { margin-bottom: 18px; display: block; color: #6366f1; text-decoration: none; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="navbar">Online Learning Management System</div>
    <div class="container">
        <h2>Manage Learner Groups</h2>
        <form method="post" style="margin-bottom: 24px;">
            <input type="text" name="group_name" placeholder="New group name" required style="width:70%;padding:8px;">
            <button type="submit" name="create_group">Create Group</button>
        </form>
        <div class="group-list">
            <?php foreach ($groups as $group): ?>
                <div class="group-item">
                    <form method="post" class="inline" style="flex:1;display:flex;align-items:center;">
                        <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
                        <input type="text" name="group_name" value="<?php echo htmlspecialchars($group['name']); ?>" style="width:70%;padding:6px;">
                        <button type="submit" name="edit_group">Edit</button>
                    </form>
                    <form method="get" class="inline">
                        <input type="hidden" name="delete" value="<?php echo $group['id']; ?>">
                        <button type="submit" onclick="return confirm('Delete this group?')">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if (empty($groups)): ?>
                <div style="color:#64748b;text-align:center;">No groups created yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

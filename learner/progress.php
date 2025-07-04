<?php
// learner/progress.php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'learner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
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

if (empty($group_ids)) {
    echo json_encode(['progress' => 0, 'total' => 0, 'completed' => 0]);
    exit;
}

// Get total assignments assigned to these groups

$in = str_repeat('?,', count($group_ids) - 1) . '?';
$sql = "SELECT id FROM assignments WHERE group_id IN ($in)";
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
$assignment_ids = [];
while ($row = $result->fetch_assoc()) {
    $assignment_ids[] = $row['id'];
}
$stmt->close();

$total = count($assignment_ids);
if ($total === 0) {
    echo json_encode(['progress' => 0, 'total' => 0, 'completed' => 0]);
    exit;
}

// Get number of assignments submitted by learner

$in2 = str_repeat('?,', count($assignment_ids) - 1) . '?';
$sql = "SELECT COUNT(*) as submitted FROM submissions WHERE assignment_id IN ($in2) AND learner_id = ? AND status IN ('submitted','graded')";
$stmt = $conn->prepare($sql);
$types2 = str_repeat('i', count($assignment_ids)) . 'i';
$params2 = [];
$params2[] = & $types2;
foreach ($assignment_ids as $k => $aid) {
    $params2[] = & $assignment_ids[$k];
}
$params2[] = & $learner_id;
call_user_func_array([$stmt, 'bind_param'], $params2);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$completed = $row['submitted'];
$stmt->close();

$progress = round(($completed / $total) * 100);

echo json_encode(['progress' => $progress, 'total' => $total, 'completed' => $completed]);

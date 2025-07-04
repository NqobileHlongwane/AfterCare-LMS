<?php
// register.php
require 'config.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $grade = isset($_POST['grade']) ? trim($_POST['grade']) : null;

    if ($name && $email && $password && $role) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, grade) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $name, $email, $hash, $role, $grade);
        if ($stmt->execute()) {
            $message = 'Registration successful! You can now <a href="login.php">login</a>.';
        } else {
            $message = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = 'Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - LMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Register</h2>
    <form method="post">
        <label>Name: <input type="text" name="name" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <label>Role:
            <select name="role" id="role" required onchange="toggleGrade()">
                <option value="learner">Learner</option>
                <option value="teacher">Teacher</option>
            </select>
        </label><br>
        <div id="gradeField">
            <label>Grade: <input type="text" name="grade"></label><br>
        </div>
        <button type="submit">Register</button>
    </form>
    <p><?php echo $message; ?></p>
    <script>
    function toggleGrade() {
        var role = document.getElementById('role').value;
        document.getElementById('gradeField').style.display = (role === 'learner') ? 'block' : 'none';
    }
    toggleGrade();
    </script>
</body>
</html>

<?php
// login.php
require 'config.php';
$message = '';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hash, $role);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;
            // Redirect based on role
            if ($role === 'learner') {
                header('Location: index.html');
            } elseif ($role === 'teacher') {
                header('Location: index.php');
            } else {
                header('Location: admin_dashboard.html');
            }
            exit();
        } else {
            $message = 'Invalid password.';
        }
    } else {
        $message = 'No user found with that email.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - LMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Login</h2>
    <form method="post">
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Login</button>
    </form>
    <p><?php echo $message; ?></p>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>

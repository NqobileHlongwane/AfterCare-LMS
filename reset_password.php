<?php
// reset_password.php
require 'config.php';
$token = isset($_GET['token']) ? $_GET['token'] : '';
$show_form = false;
if ($token) {
    $stmt = $conn->prepare('SELECT id, token_expiration FROM users WHERE password_reset_token=?');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $token_expiration);
        $stmt->fetch();
        if (strtotime($token_expiration) > time()) {
            $show_form = true;
        } else {
            $msg = 'Reset link expired. Please request a new one.';
        }
    } else {
        $msg = 'Invalid reset token.';
    }
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $stmt = $conn->prepare('SELECT id FROM users WHERE password_reset_token=? AND token_expiration > NOW()');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        if ($password === $confirm && strlen($password) >= 6) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt->close();
            $stmt = $conn->prepare('UPDATE users SET password=?, password_reset_token=NULL, token_expiration=NULL WHERE id=?');
            $stmt->bind_param('si', $hash, $user_id);
            $stmt->execute();
            $msg = 'Password reset successful. <a href="login.php">Login</a>';
            $show_form = false;
        } else {
            $msg = 'Passwords do not match or are too short (min 6 chars).';
            $show_form = true;
        }
    } else {
        $msg = 'Invalid or expired token.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width:400px;margin:60px auto;">
        <h2>Reset Password</h2>
        <?php if (!empty($msg)) echo '<div style="color:#334155;margin-bottom:18px;">' . $msg . '</div>'; ?>
        <?php if ($show_form): ?>
        <form method="post">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <label for="password">New Password:</label><br>
            <input type="password" name="password" id="password" required minlength="6" style="width:100%;padding:10px;margin:12px 0 8px 0;">
            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" name="confirm_password" id="confirm_password" required minlength="6" style="width:100%;padding:10px;margin:12px 0 18px 0;">
            <button type="submit" style="width:100%;padding:10px 0;background:#6366f1;color:#fff;border:none;border-radius:6px;font-size:1.1rem;font-weight:700;">Reset Password</button>
        </form>
        <?php endif; ?>
        <div style="margin-top:18px;"><a href="login.php" style="color:#6366f1;">Back to Login</a></div>
    </div>
</body>
</html>

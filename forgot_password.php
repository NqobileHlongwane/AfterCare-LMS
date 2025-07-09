<?php
// forgot_password.php
require 'config.php';
require 'vendor/autoload.php'; // Make sure Composer's autoload is included

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare('UPDATE users SET password_reset_token=?, token_expiration=? WHERE id=?');
        $stmt->bind_param('ssi', $token, $expires, $user_id);
        $stmt->execute();
        $stmt->close();

        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'nqobiletototo@gmail.com';       // Your Gmail
            $mail->Password   = 'rvajqecpufkdtxvr';          // App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('nqobiletototo@gmail.com', 'Nqobile Tototo'); // Change to your name
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click the link below to reset your password (valid for 30 minutes):<br><a href='$reset_link'>$reset_link</a>";
            $mail->AltBody = "Copy and paste this link in your browser: $reset_link";

            $mail->send();
            $msg = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $msg = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $msg = "No account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width:400px;margin:60px auto;">
        <h2>Forgot Password</h2>
        <?php if (!empty($msg)) echo '<div style="color:#334155;margin-bottom:18px;">' . htmlspecialchars($msg) . '</div>'; ?>
        <form method="post">
            <label for="email">Enter your registered email address:</label><br>
            <input type="email" name="email" id="email" required style="width:100%;padding:10px;margin:12px 0 18px 0;">
            <button type="submit" style="width:100%;padding:10px 0;background:#6366f1;color:#fff;border:none;border-radius:6px;font-size:1.1rem;font-weight:700;">Send Reset Link</button>
        </form>
        <div style="margin-top:18px;"><a href="login.php" style="color:#6366f1;">Back to Login</a></div>
    </div>
</body>
</html>
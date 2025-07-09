<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure this path is correct

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'nqobiletototo@gmail.com';         // Your Gmail address
    $mail->Password   = 'rvajqecpufkdtxvr';    // Your App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('nqobiletototo@gmail.com', 'Nqobile Tototo'); // Change to your name
    $mail->addAddress('nqobilehlongwane708@gmail.com', 'Nqobile Hlongwane'); // Change to your test email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test';
    $mail->Body    = '🎉 This is a test email sent using <strong>PHPMailer</strong> and Gmail SMTP.';
    $mail->AltBody = 'This is a test email sent using PHPMailer and Gmail SMTP.';

    $mail->send();
    echo '✅ Message has been sent successfully!';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
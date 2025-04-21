<?php
// send_email_otp.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // if using Composer
// OR require 'PHPMailer/PHPMailerAutoload.php'; if manually downloaded

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@gmail.com';      // Your email
    $mail->Password   = 'your_app_password';         // App-specific password (not Gmail login)
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('your_email@gmail.com', 'Matrimony OTP');
    $mail->addAddress($userInput);                   // User's email

    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body    = "Your OTP is: <b>$otp</b>";

    $mail->send();
    echo "✅ OTP sent to email!";
} catch (Exception $e) {
    echo "❌ OTP email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
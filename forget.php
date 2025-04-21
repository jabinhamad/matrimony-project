<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

$conn = new mysqli("localhost", "root", "", "matrimony",3307);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);

    $_SESSION['email'] = $email;
    $_SESSION['otp'] = $otp;

    // Check if email already exists
    $check = $conn->query("SELECT * FROM forget_pass WHERE email='$email'");
    if ($check->num_rows > 0) {
        date_default_timezone_set('Asia/Kolkata');
        $now = date('Y-m-d H:i:s');
        $conn->query("UPDATE forget_pass SET otp='$otp', otp_created='$now' WHERE email='$email'");
            } else {
                $conn->query("INSERT INTO forget_pass (email, otp, otp_created) VALUES ('$email', '$otp', '$now')");
            }

    // Send Email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jabinahamad6@gmail.com'; // Your email
        $mail->Password = 'vxer pcbr umny vzcs';   // Your Gmail app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('jabinahamad6@gmail.com', 'Matrimony Team');
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP code is: $otp";

        $mail->send();

        echo "<form method='POST' action='verifyotp.php'>
                <h2>Enter OTP</h2>
                <label>OTP:</label><br>
                <input type='number' name='otp' required><br><br>
                <button type='submit'>Verify OTP</button>
              </form>";
    } catch (Exception $e) {
        echo "Message could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>

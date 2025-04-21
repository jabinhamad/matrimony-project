
<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

// Set timezone to India Standard Time
date_default_timezone_set("Asia/Kolkata");

$conn = new mysqli("localhost", "root", "", "matrimony", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userInput = $_POST['user_input'];

$otp = rand(100000, 999999);
$otp_created_at = date("Y-m-d H:i:s");

// Check if it's email or phone
$field = filter_var($userInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

// Check if user exists
$checkSql = "SELECT * FROM login WHERE $field = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("s", $userInput);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update OTP
    $updateSql = "UPDATE login SET otp = ?, otp_created_at = ?, is_verified = 0 WHERE $field = ?";
    $update = $conn->prepare($updateSql);
    $update->bind_param("sss", $otp, $otp_created_at, $userInput);
    $update->execute();
} else {
    // Insert new user
    $insertSql = "INSERT INTO login ($field, otp, otp_created_at, is_verified) VALUES (?, ?, ?, 0)";
    $insert = $conn->prepare($insertSql);
    $insert->bind_param("sss", $userInput, $otp, $otp_created_at);
    $insert->execute();
}

// Send OTP
if ($field === 'email') {
    // Send OTP via Email
    $to = $userInput;
    $subject = "Your OTP for Verification";
    $message = "Dear user,\n\nYour OTP is: $otp\nGenerated at: $otp_created_at\n\nThanks,\nMatrimony Team";
    $headers = "From: noreply@matrimony.com";

    if (mail($to, $subject, $message, $headers)) {
        echo "OTP sent to email: $to";
    } else {
        echo "Failed to send OTP email.";
    }
} else {
    // Send OTP via SMS (placeholder)
    echo "OTP sent to phone: $userInput (Demo only: $otp)";
}
?>

<?php
$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userInput = $_POST['user_input'];
$enteredOtp = $_POST['otp'];
$field = filter_var($userInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

$sql = "SELECT * FROM login WHERE $field = ? AND otp = ? AND is_verified = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $userInput, $enteredOtp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $otpTime = strtotime($row['otp_created_at']);
    $currentTime = time();
    $difference = $currentTime - $otpTime;

    if ($difference <= 300) { // 5 minutes
        $updateSql = "UPDATE login SET is_verified = 1 WHERE $field = ?";
        $update = $conn->prepare($updateSql);
        $update->bind_param("s", $userInput);
        $update->execute();
        echo "✅ OTP Verified! You are now logged in.";
    } else {
        echo "❌ OTP Expired. Please request a new one.";
    }
} else {
    echo "❌ Invalid OTP or already verified!";
}
?>

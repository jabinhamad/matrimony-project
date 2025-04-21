<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Response message
$response = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    date_default_timezone_set('Asia/Kolkata');

    // Connect to database
    $conn = new mysqli("localhost", "root", "", "matrimony", 3307);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize phone number
    $phone = trim($_POST['phone']);
    if (empty($phone)) {
        $response = "❌ Phone number is required.";
    } else {
        $otp = rand(100000, 999999);
        $otp_created_at = date("Y-m-d H:i:s");

        // Check if phone already exists
        $checkSql = "SELECT * FROM login WHERE phone = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing record
            $updateSql = "UPDATE login SET otp = ?, otp_created_at = ?, is_verified = 0 WHERE phone = ?";
            $update = $conn->prepare($updateSql);
            $update->bind_param("sss", $otp, $otp_created_at, $phone);
            $update->execute();
        } else {
            // Insert new record
            $insertSql = "INSERT INTO login (phone, otp, otp_created_at, is_verified) VALUES (?, ?, ?, 0)";
            $insert = $conn->prepare($insertSql);
            $insert->bind_param("sss", $phone, $otp, $otp_created_at);
            $insert->execute();
        }

    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send OTP</title>
</head>
<body>
    <h2>Enter Your Phone Number</h2>
    <form method="POST" action="">
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" required>
        <br><br>
        <button type="submit">Send OTP</button>
    </form>

    <?php if (!empty($response)) : ?>
        <p><strong><?php echo $response; ?></strong></p>
    <?php endif; ?>
</body>
</html>

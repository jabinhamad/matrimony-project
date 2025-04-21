<?php
session_start();
$conn = new mysqli("localhost", "root", "", "matrimony",3307);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredOtp = $_POST['otp'];
    $email = $_SESSION['email'];

    $result = $conn->query("SELECT otp FROM forget_pass WHERE email='$email'");
    $row = $result->fetch_assoc();

    if ($row && $enteredOtp == $row['otp']) {
        header("Location: resetpass.php");
        exit();
    } else {
        echo "Invalid OTP!";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
</head>
<body>
    <h2>Enter OTP</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label>OTP:</label><br>
        <input type="text" name="otp" required><br><br>

        <button type="submit">Verify</button>
    </form>
</body>
</html>

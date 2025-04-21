<?php
session_start();
$conn = new mysqli("localhost", "root", "", "matrimony",3307);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['email'];
    $newpass = $_POST['newpass'];
    $conpass = $_POST['conpass'];

    if ($newpass === $conpass) {
        $conn->query("UPDATE forget_pass SET newpass='$newpass', conpass='$conpass' WHERE email='$email'");
        echo "Password updated successfully!";
        session_destroy();
    } else {
        echo "Passwords do not match!";
    }
} else {
?>
<form method="POST">
    <h2>Reset Your Password</h2>
    <label>New Password:</label><br>
    <input type="pass" name="newpass" required><br><br>
    <label>Confirm Password:</label><br>
    <input type="pass" name="conpass" required><br><br>
    <button type="submit">Reset Password</button>
</form>
<?php } ?>

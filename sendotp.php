<?php
$authKey = "your_actual_auth_key"; // Replace with your real MSG91 Auth Key
$templateId = "your_template_id"; // Replace with your real MSG91 Flow Template ID
$senderId = "your_sender_id"; // Replace with your approved sender ID

// Function to connect to the database
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "matrimony";
    $port = 3307;

    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Connect to DB
$conn = getDbConnection();

// Get user input (mobile or email)
$userInput = $_POST['phone_or_email']; // From your login form

// Validate input (basic)
if (empty($userInput)) {
    die("Please enter a valid phone number or email.");
}

// Check if user exists
$query = "SELECT * FROM login WHERE phone = '$userInput' OR email = '$userInput'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $phone = $user['phone'];

    // Ensure phone starts with 91 (India country code)
    if (!str_starts_with($phone, '91')) {
        $phone = '91' . $phone;
    }

    // Generate 6-digit OTP
    $otp = rand(100000, 999999);
    $otp_created_at = date('Y-m-d H:i:s');

    // Update the login table
    $update = "UPDATE login 
               SET otp = '$otp', otp_created_at = '$otp_created_at', is_verified = 0 
               WHERE id = {$user['id']}";
    $conn->query($update);

    // Prepare MSG91 payload
    $data = [
        'template_id' => $templateId,
        'sender' => $senderId,
        'mobiles' => $phone,
        'VAR1' => $otp
    ];

    // Send OTP via MSG91 API
    $ch = curl_init("https://control.msg91.com/api/v5/flow/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authkey: $authKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        echo "OTP sent successfully to: " . substr($phone, -10);
    } else {
        echo "Failed to send OTP. Please try again.";
        // echo $response; // Uncomment to debug
    }
} else {
    echo "User not found.";
}

$conn->close();
?>

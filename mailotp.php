<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
date_default_timezone_set("Asia/Kolkata");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Handle both JSON (e.g. from Postman) and regular POST (e.g. from forms)
$input = json_decode(file_get_contents('php://input'), true);

// Fallback to GET if POST (JSON) input is empty
if (empty($input)) {
    $input = $_GET;
}

// Try JSON input first
$email = isset($input['email']) ? trim($input['email']) : null;
$phone = isset($input['phone']) ? trim($input['phone']) : null;

// Fallback to regular POST if JSON input is empty
if ($email === null && $phone === null) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
}

if (empty($email) && empty($phone)) {
    echo json_encode(["status" => "error", "message" => "Email or phone is required"]);
    exit;
}

$response = [];

// Handle GET requests to fetch OTP
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch OTP if email or phone exists in the database
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT otp, otp_created_at FROM login WHERE email = ?");
        $stmt->bind_param("s", $email);
    } else {
        $stmt = $conn->prepare("SELECT otp, otp_created_at FROM login WHERE phone = ?");
        $stmt->bind_param("s", $phone);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $otp = $row['otp'];
        $otp_created_at = $row['otp_created_at'];

        $response["status"] = "success";
        $response["message"] = "OTP retrieved";
        $response["otp"] = $otp; // You can choose not to send OTP in production.
        $response["otp_created_at"] = $otp_created_at;
    } else {
        $response["status"] = "error";
        $response["message"] = "No OTP found for this email or phone";
    }

    echo json_encode($response);
    exit;
}

// Handle POST requests to generate and send OTP
$otp = rand(100000, 999999);
$otp_created_at = date("Y-m-d H:i:s");

// Check if user already exists
if (!empty($email)) {
    $stmt = $conn->prepare("SELECT * FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
} else {
    $stmt = $conn->prepare("SELECT * FROM login WHERE phone = ?");
    $stmt->bind_param("s", $phone);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    if (!empty($email)) {
        $update = $conn->prepare("UPDATE login SET otp = ?, otp_created_at = ?, is_verified = 0 WHERE email = ?");
        $update->bind_param("sss", $otp, $otp_created_at, $email);
    } else {
        $update = $conn->prepare("UPDATE login SET otp = ?, otp_created_at = ?, is_verified = 0 WHERE phone = ?");
        $update->bind_param("sss", $otp, $otp_created_at, $phone);
    }
    $update->execute();
} else {
    $insert = $conn->prepare("INSERT INTO login (email, phone, otp, otp_created_at, is_verified) VALUES (?, ?, ?, ?, 0)");
    $insert->bind_param("ssss", $email, $phone, $otp, $otp_created_at);
    $insert->execute();
}

$response = [];

// Send OTP via Email using PHPMailer
if (!empty($email)) {
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
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Verification';
        $mail->Body = "Hi,<br>Your OTP is: <strong>$otp</strong><br><br>Generated at: $otp_created_at<br>Thanks,<br>Matrimony Team";

        $mail->send();
        $response["email"] = "OTP sent to email: $email";
    } catch (Exception $e) {
        $response["email_error"] = $mail->ErrorInfo;
    }
}

// Send OTP via SMS using 2Factor API
if (!empty($phone)) {
    // Clean phone number and format to E.164 (India: 91XXXXXXXXXX)
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($clean_phone) === 10) {
        $clean_phone = "91" . $clean_phone;
    }

    // 2Factor API settings
    $apiKey = "7739c6ca-f6bf-42eb-b77c-ffcd180ea56e"; // Replace with your valid API key
    $templateName = urlencode("MATRIMONY");      // Exact approved template name
    $sender_id = "MATMON";                           // Approved sender ID
    $url = "https://2factor.in/API/V1/$apiKey/SMS/$clean_phone/$otp/$templateName?sender_id=$sender_id&unicode=1";

    // Initialize cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    ]);

    $sms_response = curl_exec($curl);
    $sms_error = curl_error($curl);
    curl_close($curl);

    $response["sms_debug"] = [
        "api_url" => $url,
        "phone_sent" => $clean_phone,
        "raw_response" => $sms_response,
    ];

    if ($sms_error) {
        $response["sms_error"] = $sms_error;
    } else {
        $result = json_decode($sms_response, true);
        if (!$result) {
            $response["sms_json_error"] = json_last_error_msg();
        } elseif (isset($result['Status']) && $result['Status'] === 'Success') {
            $response["sms"] = "OTP sent to phone: $clean_phone";
            $_SESSION['otp_session_id'] = $result['Details'];
        } else {
            $response["sms_failed"] = $result['Details'] ?? 'Unknown SMS error';
        }
    }
}

// Final response
echo json_encode([
    "status" => "success",
    "message" => "OTP processed",
    "otp" => $otp, // optional: remove this in production
    "details" => $response
]);
?>

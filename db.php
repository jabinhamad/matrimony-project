<?php
$host = "localhost";
$user = "root";
$password = ""; // Leave blank if you're using XAMPP default
$database = "matrimony";
$port = 3307;

$conn = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

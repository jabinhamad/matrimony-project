<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to DB
$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect filters
$conditions = [];
$params = [];

if (!empty($_GET['min_age'])) {
    $conditions[] = "age >= ?";
    $params[] = (int)$_GET['min_age'];
}
if (!empty($_GET['max_age'])) {
    $conditions[] = "age <= ?";
    $params[] = (int)$_GET['max_age'];
}
if (!empty($_GET['min_height'])) {
    $conditions[] = "height >= ?";
    $params[] = $_GET['min_height'];
}
if (!empty($_GET['max_height'])) {
    $conditions[] = "height <= ?";
    $params[] = $_GET['max_height'];
}
if (!empty($_GET['marital_status'])) {
    $conditions[] = "marital_status = ?";
    $params[] = $_GET['marital_status'];
}
if (!empty($_GET['religion'])) {
    $conditions[] = "religion = ?";
    $params[] = $_GET['religion'];
}
if (!empty($_GET['caste'])) {
    $conditions[] = "caste = ?";
    $params[] = $_GET['caste'];
}
if (!empty($_GET['education'])) {
    $conditions[] = "education = ?";
    $params[] = $_GET['education'];
}
if (!empty($_GET['income'])) {
    $conditions[] = "annual_income = ?";
    $params[] = $_GET['income'];
}
if (!empty($_GET['place'])) {
    $conditions[] = "(state LIKE ? OR city LIKE ?)";
    $params[] = "%" . $_GET['place'] . "%";
    $params[] = "%" . $_GET['place'] . "%";
}

// Build SQL
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
$sql = "SELECT * FROM profile_details $where";

// Prepare statement
$stmt = $conn->prepare($sql);
if ($stmt && !empty($params)) {
    $types = str_repeat("s", count($params)); // all are strings, except age which is int
    $stmt->bind_param($types, ...$params);
}

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Query preparation failed: " . $conn->error);
}

// Display
echo "<h2>Matching Profiles</h2>";
if ($result->num_rows > 0) {
    echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";

    while ($row = $result->fetch_assoc()) {
        $imagePath = 'data:image/jpeg;base64,' . base64_encode($row['profile_image']);
        echo "<div style='margin: 10px; text-align: center; width: 150px; padding: 10px; border: 1px solid #ddd; border-radius: 10px;'>
            <img src='$imagePath' style='width: 100px; height: 100px; object-fit: cover; border-radius: 50%;'><br><br>
            <strong>{$row['profile_fname']}, {$row['age']}</strong><br>
            {$row['caste']}, {$row['city']}
        </div>";
    }

    echo "</div>";
} else {
    echo "<p>No matching profiles found.</p>";
}

// Close connection
$conn->close();
?>

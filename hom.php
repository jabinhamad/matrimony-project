<?php
// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'matrimony', 3307);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get values from the query string (used in similar matches)
$min_age = isset($_GET['min_age']) ? $_GET['min_age'] : '';
$max_age = isset($_GET['max_age']) ? $_GET['max_age'] : '';
$min_height = isset($_GET['min_height']) ? $_GET['min_height'] : '';
$max_height = isset($_GET['max_height']) ? $_GET['max_height'] : '';
$marital_status = isset($_GET['marital_status']) ? $_GET['marital_status'] : '';
$religion = isset($_GET['religion']) ? $_GET['religion'] : '';
$caste = isset($_GET['caste']) ? $_GET['caste'] : '';
$education = isset($_GET['education']) ? $_GET['education'] : '';
$income = isset($_GET['income']) ? $_GET['income'] : '';
$place = isset($_GET['place']) ? $_GET['place'] : '';
$property = isset($_GET['property']) ? $_GET['property'] : '';

// ✅ 1. All Profiles
$query_all_matches = "SELECT * FROM profile_details";

// ✅ 2. New Profiles (added in last 24 hours)
$query_new_matches = "SELECT * FROM profile_details WHERE created_at >= NOW() - INTERVAL 1 DAY";

// ✅ 3. Similar Matches based on expectations
$query_similar_matches = "
    SELECT pd.*
    FROM profile_details pd
    WHERE pd.age BETWEEN '$min_age' AND '$max_age'
    AND pd.height BETWEEN '$min_height' AND '$max_height'
    AND pd.marital_status = '$marital_status'
    AND pd.religion = '$religion'
    AND pd.caste = '$caste'
    AND pd.education = '$education'
    AND pd.annual_income = '$income'
    AND pd.city LIKE '%" . $mysqli->real_escape_string($place) . "%'
";

// Execute queries
$result_all_matches = $mysqli->query($query_all_matches);
$result_new_matches = $mysqli->query($query_new_matches);
$result_similar_matches = $mysqli->query($query_similar_matches);

// Output format function
function display_profiles($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px; display:flex; align-items:center;'>";

        // Convert BLOB to Base64 for image display
        if (!empty($row['profile_image'])) {
            $imgData = base64_encode($row['profile_image']);
            $imgSrc = 'data:image/jpeg;base64,' . $imgData;
            echo "<img src='$imgSrc' alt='Profile Image' style='width:100px; height:120px; object-fit:cover; border:1px solid #aaa; margin-right:15px;'>";
        } else {
            // Display default image if no photo
            echo "<img src='default.png' alt='No Image' style='width:100px; height:120px; object-fit:cover; border:1px solid #aaa; margin-right:15px;'>";
        }

        // Profile Details
        echo "<div>";
        echo "Name: " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "<br>";
        echo "Age: " . htmlspecialchars($row['age']) . "<br>";
        echo "City: " . htmlspecialchars($row['city']) . "<br>";
        echo "Income: " . htmlspecialchars($row['annual_income']) . "<br>";
        echo "</div>";

        echo "</div>";
    }
}

// Show All Matches
echo "<h3>All Matches</h3>";
if ($result_all_matches->num_rows > 0) {
    display_profiles($result_all_matches);
} else {
    echo "No matches found.";
}

// Show New Matches (last 24 hours)
echo "<h3>New Matches</h3>";
if ($result_new_matches->num_rows > 0) {
    display_profiles($result_new_matches);
} else {
    echo "No new matches found.";
}

// Show Similar Matches
echo "<h3>Similar Matches</h3>";
if ($result_similar_matches->num_rows > 0) {
    display_profiles($result_similar_matches);
} else {
    echo "No similar matches found.";
}

$mysqli->close();
?>

<?php
session_start();

// DB Connection
$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $profile_name = $_POST['profile_name'] ?? '';
    $profile_fname = $_POST['profile_fname'] ?? '';
    $profile_lname = $_POST['profile_lname'] ?? '';
    $age = $_POST['age'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $height = $_POST['height'] ?? '';
    $marital_status = $_POST['marital_status'] ?? '';
    $religion = $_POST['religion'] ?? '';
    $caste = $_POST['caste'] ?? '';
    $state = $_POST['state'] ?? '';
    $city = $_POST['city'] ?? '';
    $mother_tongue = $_POST['mother_tongue'] ?? '';
    $education = $_POST['education'] ?? '';
    $employment_type = $_POST['employment_type'] ?? '';
    $occupation = $_POST['occupation'] ?? '';
    $annual_income = $_POST['annual_income'] ?? '';

    $created_at = date("Y-m-d H:i:s");

    // Prepare SQL (include ? for image)
    $stmt = $conn->prepare("INSERT INTO profile_details (
        first_name, last_name, profile_name, profile_fname, profile_lname, age, dob, height, marital_status,
        religion, caste, state, city, mother_tongue, education, employment_type, occupation, annual_income, profile_image, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Null image_data placeholder
    $image_data = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $image_data = file_get_contents($_FILES['profile_image']['tmp_name']);
    }

    // Bind parameters (use 's' for all strings and 'b' for blob)
    $stmt->bind_param("ssssssssssssssssssbs",
        $first_name, $last_name, $profile_name, $profile_fname, $profile_lname, $age, $dob, $height, $marital_status,
        $religion, $caste, $state, $city, $mother_tongue, $education, $employment_type, $occupation, $annual_income, $null, $created_at
    );

    // Send blob data
    if ($image_data !== null) {
        $stmt->send_long_data(18, $image_data); // 18 is the index of 'profile_image'
    }

    // Execute
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Profile saved successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error saving profile: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>

<!-- HTML Form -->
<h2>Create New Profile</h2>
<form method="POST" action="" enctype="multipart/form-data">
    First Name: <input type="text" name="first_name"><br><br>
    Last Name: <input type="text" name="last_name"><br><br>

    Profile For:
    <select name="profile_name">
        <option value="Myself">Myself</option>
        <option value="Son">Son</option>
        <option value="Daughter">Daughter</option>
        <option value="Brother">Brother</option>
        <option value="Sister">Sister</option>
        <option value="Friend">Friend</option>
        <option value="Relative">Relative</option>
    </select><br><br>

    Profile First Name: <input type="text" name="profile_fname"><br><br>
    Profile Last Name: <input type="text" name="profile_lname"><br><br>
    Age: <input type="number" name="age"><br><br>
    DOB: <input type="date" name="dob"><br><br>
    Height: <input type="text" name="height"><br><br>

    Marital Status:
    <select name="marital_status">
        <option value="Single">Single</option>
        <option value="Married">Married</option>
        <option value="Divorced">Divorced</option>
        <option value="Widowed">Widowed</option>
    </select><br><br>

    Religion: <input type="text" name="religion"><br><br>
    Caste: <input type="text" name="caste"><br><br>
    State: <input type="text" name="state"><br><br>
    City: <input type="text" name="city"><br><br>
    Mother Tongue: <input type="text" name="mother_tongue"><br><br>
    Education: <input type="text" name="education"><br><br>
    Employment Type: <input type="text" name="employment_type"><br><br>
    Occupation: <input type="text" name="occupation"><br><br>
    Annual Income: <input type="text" name="annual_income"><br><br>

    Profile Image: <input type="file" name="profile_image"><br><br>

    <button type="submit">Submit</button>
</form>

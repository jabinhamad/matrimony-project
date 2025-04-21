<?php
session_start();

// DB Connection
$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
if ($conn->connect_error) {
    die("❌ DB Connection failed: " . $conn->connect_error);
}

// Update Profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['id'])) {
    $profile_id = $_GET['id'];

    // Get form fields
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

    $image_data = null;
    $image_name = null;

    // Check if new image uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $image_data = file_get_contents($_FILES['profile_image']['tmp_name']);
        $image_name = $_FILES['profile_image']['name'];
    } else {
        // Fetch existing image and name
        $img_stmt = $conn->prepare("SELECT profile_image, profile_image_name FROM profile_details WHERE id = ?");
        $img_stmt->bind_param("i", $profile_id);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        if ($img_result->num_rows > 0) {
            $existing_img = $img_result->fetch_assoc();
            $image_data = $existing_img['profile_image'];
            $image_name = $existing_img['profile_image_name'];
        }
        $img_stmt->close();
    }

    // Update profile
    $stmt = $conn->prepare("UPDATE profile_details SET 
        first_name = ?, last_name = ?, profile_name = ?, profile_fname = ?, profile_lname = ?, age = ?, dob = ?, height = ?, marital_status = ?, 
        religion = ?, caste = ?, state = ?, city = ?, mother_tongue = ?, education = ?, employment_type = ?, occupation = ?, annual_income = ?, 
        profile_image = ?, profile_image_name = ? WHERE id = ?");

    $stmt->send_long_data(19, $image_data);
    $stmt->bind_param("ssssssssssssssssssssi", 
        $first_name, $last_name, $profile_name, $profile_fname, $profile_lname, $age, $dob, $height, $marital_status,
        $religion, $caste, $state, $city, $mother_tongue, $education, $employment_type, $occupation, $annual_income,
        $image_data, $image_name, $profile_id
    );

    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Profile updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Query Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Fetch profile
if (isset($_GET['id'])) {
    $profile_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM profile_details WHERE id = ?");
    $stmt->bind_param("i", $profile_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $profile = $result->fetch_assoc();
    } else {
        echo "❌ Profile not found.";
        exit;
    }
} else {
    echo "❌ No profile ID provided.";
    exit;
}

$conn->close();
?>

<!-- Profile Update Form -->
<form method="POST" enctype="multipart/form-data">
    First Name: <input type="text" name="first_name" value="<?= $profile['first_name'] ?? '' ?>"><br><br>
    Last Name: <input type="text" name="last_name" value="<?= $profile['last_name'] ?? '' ?>"><br><br>

    Profile For:
    <select name="profile_name">
        <?php
        $options = ["Myself", "Son", "Daughter", "Brother", "Sister", "Friend", "Relative"];
        foreach ($options as $opt) {
            $selected = ($profile['profile_name'] == $opt) ? 'selected' : '';
            echo "<option value='$opt' $selected>$opt</option>";
        }
        ?>
    </select><br><br>

    Profile First Name: <input type="text" name="profile_fname" value="<?= $profile['profile_fname'] ?? '' ?>"><br><br>
    Profile Last Name: <input type="text" name="profile_lname" value="<?= $profile['profile_lname'] ?? '' ?>"><br><br>
    Age: <input type="number" name="age" value="<?= $profile['age'] ?? '' ?>"><br><br>
    DOB: <input type="date" name="dob" value="<?= $profile['dob'] ?? '' ?>"><br><br>
    Height: <input type="text" name="height" value="<?= $profile['height'] ?? '' ?>"><br><br>

    Marital Status:
    <select name="marital_status">
        <?php
        $statuses = ["Single", "Married", "Divorced", "Widowed"];
        foreach ($statuses as $status) {
            $selected = ($profile['marital_status'] == $status) ? 'selected' : '';
            echo "<option value='$status' $selected>$status</option>";
        }
        ?>
    </select><br><br>

    Religion: <input type="text" name="religion" value="<?= $profile['religion'] ?? '' ?>"><br><br>
    Caste: <input type="text" name="caste" value="<?= $profile['caste'] ?? '' ?>"><br><br>
    State: <input type="text" name="state" value="<?= $profile['state'] ?? '' ?>"><br><br>
    City: <input type="text" name="city" value="<?= $profile['city'] ?? '' ?>"><br><br>
    Mother Tongue: <input type="text" name="mother_tongue" value="<?= $profile['mother_tongue'] ?? '' ?>"><br><br>
    Education: <input type="text" name="education" value="<?= $profile['education'] ?? '' ?>"><br><br>
    Employment Type: <input type="text" name="employment_type" value="<?= $profile['employment_type'] ?? '' ?>"><br><br>
    Occupation: <input type="text" name="occupation" value="<?= $profile['occupation'] ?? '' ?>"><br><br>
    Annual Income: <input type="text" name="annual_income" value="<?= $profile['annual_income'] ?? '' ?>"><br><br>

    <!-- Show current image filename if available -->
    <?php if (!empty($profile['profile_image_name'])): ?>
        <p>Current Profile Image: <?= htmlspecialchars($profile['profile_image_name']) ?></p>
    <?php else: ?>
        <p>No profile image uploaded.</p>
    <?php endif; ?>

    <label for="profile_image">Profile Image:</label>
    <input type="file" name="profile_image" id="profile_image" onchange="updateFileName()">
    <br><br>

    <button type="submit">Update Profile</button>
</form>

<!-- JavaScript to show selected file name -->
<script>
function updateFileName() {
    var fileInput = document.getElementById('profile_image');
    var fileName = fileInput.files.length > 0 ? fileInput.files[0].name : 'No file chosen';
    fileInput.previousElementSibling.innerText = 'Profile Image: ' + fileName;
}
</script>

<?php
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
if ($conn->connect_error) {
    die("❌ DB Connection failed: " . $conn->connect_error);
}

$profile = [];
$profile_id = null;

// Get ID from URL
if (isset($_GET['id'])) {
    $profile_id = $_GET['id'];

    $sql = "SELECT * FROM profile_details WHERE id = ?";
    $stmt = $conn->prepare($sql);
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-2xl mx-auto mt-6 bg-white rounded-xl shadow-md overflow-hidden p-6">
    <h2 class="text-xl font-semibold mb-4">Edit Profile</h2>

    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="profile_id" value="<?= htmlspecialchars($profile['id']) ?>">

        <!-- First Name -->
        <div class="mb-4">
            <label class="block font-medium">First Name</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name']) ?>" class="w-full p-2 border border-gray-300 rounded" required>
        </div>

        <!-- Last Name -->
        <div class="mb-4">
            <label class="block font-medium">Last Name</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name']) ?>" class="w-full p-2 border border-gray-300 rounded" required>
        </div>

        <!-- Profile For -->
        <div class="mb-4">
            <label class="block font-medium">Profile For</label>
            <select name="profile_name" class="w-full p-2 border border-gray-300 rounded">
                <?php
                $options = ['Myself', 'Son', 'Daughter', 'Brother', 'Sister', 'Friend', 'Relative'];
                foreach ($options as $opt) {
                    $selected = ($profile['profile_name'] ?? '') === $opt ? 'selected' : '';
                    echo "<option value='$opt' $selected>$opt</option>";
                }
                ?>
            </select>
        </div>

        <!-- DOB -->
        <div class="mb-4">
            <label class="block font-medium">Date of Birth</label>
            <input type="date" name="dob" value="<?= htmlspecialchars($profile['dob']) ?>" class="w-full p-2 border border-gray-300 rounded">
        </div>

        <!-- City -->
        <div class="mb-4">
            <label class="block font-medium">City</label>
            <input type="text" name="city" value="<?= htmlspecialchars($profile['city']) ?>" class="w-full p-2 border border-gray-300 rounded">
        </div>

        <!-- Profile Image -->
        <div class="mb-4">
            <label class="block font-medium">Profile Image</label>
            <input type="file" name="profile_image" class="w-full">
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update Profile</button>
        </div>
    </form>
  </div>
</body>
</html>

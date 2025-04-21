<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
if ($conn->connect_error) {
    die("❌ DB Connection failed: " . $conn->connect_error);
}

// Fetch latest profile (can add session filter later)
$sql = "SELECT * FROM profile_details ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $profile = $result->fetch_assoc();
    $name = $profile['profile_fname'] . ' ' . $profile['profile_lname'];
    $id_number = $profile['id']; // Or use a profile_id if available
    $image_data = base64_encode($profile['profile_image']);
    $profile_image = "data:image/jpeg;base64," . $image_data;
} else {
    $profile = null;
}
$conn->close();
?>

<?php


// Database connection
$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
if ($conn->connect_error) {
    die("❌ DB Connection failed: " . $conn->connect_error);
}

// Fetch latest profile (can add session filter later)
$sql = "SELECT * FROM profile_details ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$profile = null;
$profile_image = "";
$name = "";
$id_number = "";

if ($result && $result->num_rows > 0) {
    $profile = $result->fetch_assoc();
    $name = $profile['profile_fname'] . ' ' . $profile['profile_lname'];
    $id_number = $profile['id'];

    // Handle image
    if (!empty($profile['profile_image'])) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($profile['profile_image']);
        $profile_image = "data:$mimeType;base64," . base64_encode($profile['profile_image']);
    }
}
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-sm mx-auto mt-6 bg-white rounded-xl shadow-md overflow-hidden">

    <!-- Top Profile Area -->
    <div class="relative bg-cover bg-center p-6" style="background-image: url('https://i.imgur.com/4Z7y5.jpg');">
      <div class="flex justify-center">
        <div class="relative">
          <?php if ($profile && !empty($profile_image)): ?>
            <img src="<?= $profile_image ?>" alt="Profile" class="w-24 h-24 rounded-full border-4 border-white mx-auto" />
          <?php else: ?>
            <img src="placeholder.jpg" alt="No Image" class="w-24 h-24 rounded-full border-4 border-white mx-auto" />
          <?php endif; ?>
          <div class="absolute bottom-0 right-0 bg-blue-500 p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
              <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h3l2 2 2-2h3a2 2 0 002-2V5a2 2 0 00-2-2H4z" />
            </svg>
          </div>
        </div>
      </div>
      <div class="text-center mt-2 text-white">
        <h2 class="text-xl font-semibold"><?= $profile ? $name : 'No Profile Found' ?></h2>
        <p class="text-sm"><?= $profile ? 'ID: ' . $id_number : '' ?></p>
      </div>
    </div>

    <!-- Buttons Section -->
    <div class="p-4 space-y-3">
      <button id="toggleProfileBtn" class="flex items-center justify-between w-full px-4 py-2 bg-white shadow rounded-md hover:bg-gray-50">
        <span class="flex items-center">📝 <span class="ml-2">Edit and view profile</span></span> ➔
      </button>

      <!-- Hidden Profile Details Section -->
      <div id="profileDetails" class="hidden mt-4 bg-white p-4 rounded shadow">
        <?php if ($profile): ?>
          <div class='space-y-2'>
            <?php if (!empty($profile_image)): ?>
              <img src='<?= $profile_image ?>' alt='Profile Image' class='w-32 h-32 rounded-full object-cover mb-4'>
            <?php endif; ?>
            <p><strong>Full Name:</strong> <?= $profile['first_name'] ?> <?= $profile['last_name'] ?></p>
            <p><strong>Profile For:</strong> <?= $profile['profile_name'] ?></p>
            <p><strong>Age:</strong> <?= $profile['age'] ?></p>
            <p><strong>DOB:</strong> <?= $profile['dob'] ?></p>
            <p><strong>Height:</strong> <?= $profile['height'] ?></p>
            <p><strong>Marital Status:</strong> <?= $profile['marital_status'] ?></p>
            <p><strong>Religion:</strong> <?= $profile['religion'] ?></p>
            <p><strong>Caste:</strong> <?= $profile['caste'] ?></p>
            <p><strong>State:</strong> <?= $profile['state'] ?></p>
            <p><strong>City:</strong> <?= $profile['city'] ?></p>
            <p><strong>Mother Tongue:</strong> <?= $profile['mother_tongue'] ?></p>
            <p><strong>Education:</strong> <?= $profile['education'] ?></p>
            <p><strong>Employment Type:</strong> <?= $profile['employment_type'] ?></p>
            <p><strong>Occupation:</strong> <?= $profile['occupation'] ?></p>
            <p><strong>Annual Income:</strong> <?= $profile['annual_income'] ?></p>
          </div>

          <!-- Edit and View Buttons -->
<div class="flex space-x-3 mt-4">
  <a href="update_profile.php?id=<?= $profile['id'] ?>" class="inline-block">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">✏️ Edit Profile</button>
  </a>
  
</div>

        <?php else: ?>
          <p class="text-red-500">❌ No profile found.</p>
        <?php endif; ?>
      </div>

      <!-- Other Buttons -->
      <button class="flex items-center justify-between w-full px-4 py-2 bg-white shadow rounded-md hover:bg-gray-50">
  <span class="flex items-center">🔍 <span class="ml-2">Search by ID or Partner</span></span> ➔
</button>
      <a href="view_profile.php?id=<?= $profile['id'] ?>" class="inline-block">
      <button class="flex items-center justify-between w-full px-4 py-2 bg-white shadow rounded-md hover:bg-gray-50">
        <span class="flex items-center">⬇️ <span class="ml-2">Download and share profile</span></span> ➔
      </button>
      </a>

      <button class="flex items-center justify-between w-full px-4 py-2 bg-white shadow rounded-md hover:bg-gray-50">
        <span class="flex items-center">👑 <span class="ml-2">Premium Membership</span></span> ➔
      </button>
      <button class="flex items-center justify-between w-full px-4 py-2 bg-white shadow rounded-md hover:bg-gray-50">
        ℹ️ <span>Info</span> ➔
      </button>
      <button class="flex items-center justify-between w-full px-4 py-2 bg-white shadow rounded-md hover:bg-gray-50">
        ☎️ <span>Contact Us</span> ➔
      </button>
      <button class="flex items-center justify-between w-full px-4 py-2 bg-white shadow rounded-md hover:bg-gray-50">
        🚪 <span>Logout</span> ➔
      </button>
    </div>
  </div>

  <!-- Toggle Script -->
  <script>
    document.getElementById("toggleProfileBtn").addEventListener("click", function () {
      const details = document.getElementById("profileDetails");
      details.classList.toggle("hidden");
    });
    
    // Toggle for Edit/View Profile
document.getElementById("toggleProfileBtn").addEventListener("click", function () {
  const details = document.getElementById("profileDetails");
  details.classList.toggle("hidden");
});

// Toggle for Search Form
document.getElementById("toggleSearchBtn").addEventListener("click", function () {
  const form = document.getElementById("searchForm");
  form.classList.toggle("hidden");
});


  </script>


<!-- Hidden Search Form -->

</body>
</html>

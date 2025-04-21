<?php
$id = $_GET['id'] ?? null;

$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
if ($conn->connect_error || !$id) {
    die("❌ Connection failed or ID missing");
}

$stmt = $conn->prepare("SELECT * FROM profile_details WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$conn->close();

$profile_image = "";
if (!empty($profile['profile_image'])) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($profile['profile_image']);
    $profile_image = "data:$mimeType;base64," . base64_encode($profile['profile_image']);
}

// Download as PDF
if (isset($_GET['download_pdf']) && $profile) {
    require_once('tcpdf/tcpdf.php');

    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $pdf->Cell(0, 10, 'Profile Details', 0, 1, 'C');
    $pdf->Ln();

    $pdf->Cell(0, 10, 'Full Name: ' . $profile['first_name'] . ' ' . $profile['last_name'], 0, 1);
    $pdf->Cell(0, 10, 'Profile For: ' . $profile['profile_name'], 0, 1);
    $pdf->Cell(0, 10, 'Profile Name: ' . $profile['profile_fname'] . ' ' . $profile['profile_lname'], 0, 1);
    $pdf->Cell(0, 10, 'Age: ' . $profile['age'], 0, 1);
    $pdf->Cell(0, 10, 'DOB: ' . $profile['dob'], 0, 1);
    $pdf->Cell(0, 10, 'Height: ' . $profile['height'], 0, 1);
    $pdf->Cell(0, 10, 'Marital Status: ' . $profile['marital_status'], 0, 1);
    $pdf->Cell(0, 10, 'Religion: ' . $profile['religion'], 0, 1);
    $pdf->Cell(0, 10, 'Caste: ' . $profile['caste'], 0, 1);
    $pdf->Cell(0, 10, 'State: ' . $profile['state'], 0, 1);
    $pdf->Cell(0, 10, 'City: ' . $profile['city'], 0, 1);
    $pdf->Cell(0, 10, 'Mother Tongue: ' . $profile['mother_tongue'], 0, 1);
    $pdf->Cell(0, 10, 'Education: ' . $profile['education'], 0, 1);
    $pdf->Cell(0, 10, 'Employment Type: ' . $profile['employment_type'], 0, 1);
    $pdf->Cell(0, 10, 'Occupation: ' . $profile['occupation'], 0, 1);
    $pdf->Cell(0, 10, 'Annual Income: ' . $profile['annual_income'], 0, 1);

    $pdf->Output('profile_' . $id . '.pdf', 'D');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-md mx-auto mt-8 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4">👁️ View Profile</h2>
    <?php if ($profile): ?>
      <?php if (!empty($profile_image)): ?>
        <img src="<?= $profile_image ?>" class="w-32 h-32 rounded-full mb-4 object-cover">
      <?php endif; ?>
      <p><strong>Full Name:</strong> <?= $profile['first_name'] . ' ' . $profile['last_name'] ?></p>
      <p><strong>Profile For:</strong> <?= $profile['profile_name'] ?></p>
      <p><strong>Profile Name:</strong> <?= $profile['profile_fname'] . ' ' . $profile['profile_lname'] ?></p>
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

      <!-- Buttons -->
      <div class="mt-6 flex space-x-4">
        <a href="?id=<?= $id ?>&download_pdf=true" class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          📥 Download PDF
        </a>
        <button onclick="shareProfile()" class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
          📤 Share
        </button>
      </div>
    <?php else: ?>
      <p class="text-red-500">❌ Profile not found.</p>
    <?php endif; ?>
  </div>

  <script>
    function shareProfile() {
      if (navigator.share) {
        navigator.share({
          title: 'Matrimony Profile',
          text: 'Check out this matrimony profile',
          url: window.location.href
        }).catch(err => console.error('Sharing failed', err));
      } else {
        alert('Sharing not supported in your browser.');
      }
    }
  </script>
</body>
</html>

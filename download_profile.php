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

$profile_image = "";
if (!empty($profile['profile_image'])) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($profile['profile_image']);
    $profile_image = "data:$mimeType;base64," . base64_encode($profile['profile_image']);
}
$conn->close();

// Include TCPDF for PDF generation
require_once(__DIR__ . '/tcpdf/tcpdf.php');


// Function to generate PDF
if (isset($_GET['download_pdf'])) {
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $pdf->Cell(0, 10, 'Profile Information', 0, 1, 'C');
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Full Name: ' . $profile['first_name'] . ' ' . $profile['last_name']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Profile For: ' . $profile['profile_name']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Profile Name: ' . $profile['profile_fname'] . ' ' . $profile['profile_lname']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Age: ' . $profile['age']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'DOB: ' . $profile['dob']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Height: ' . $profile['height']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Marital Status: ' . $profile['marital_status']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Religion: ' . $profile['religion']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Caste: ' . $profile['caste']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'State: ' . $profile['state']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'City: ' . $profile['city']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Mother Tongue: ' . $profile['mother_tongue']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Education: ' . $profile['education']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Employment Type: ' . $profile['employment_type']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Occupation: ' . $profile['occupation']);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Annual Income: ' . $profile['annual_income']);

    // Output PDF
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

      <!-- Download and Share Buttons -->
      <div class="mt-4">
        <a href="?download_pdf=true" class="inline-block bg-blue-500 text-white py-2 px-4 rounded mr-4">Download as PDF</a>
        <button class="inline-block bg-green-500 text-white py-2 px-4 rounded" onclick="showShareOptions()">Share</button>
      </div>

      <!-- Share Options Modal -->
      <div id="share-options" class="hidden mt-4">
        <p class="mb-2">Share this profile:</p>
        <a href="mailto:?subject=Profile Share&body=Check out this profile: <?= $profile['first_name'] ?> <?= $profile['last_name'] ?>" class="inline-block bg-blue-500 text-white py-2 px-4 rounded mr-4">Email</a>
        <a href="https://wa.me/?text=Check%20out%20this%20profile:%20<?= urlencode($profile['first_name'] . ' ' . $profile['last_name']) ?>" class="inline-block bg-green-500 text-white py-2 px-4 rounded">WhatsApp</a>
      </div>
    <?php else: ?>
      <p class="text-red-500">❌ Profile not found.</p>
    <?php endif; ?>
  </div>

  <script>
    function showShareOptions() {
      const shareOptions = document.getElementById('share-options');
      shareOptions.classList.toggle('hidden');
    }
  </script>
</body>
</html>

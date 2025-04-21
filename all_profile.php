<?php
$conn = new mysqli("localhost", "root", "", "matrimony", 3307);
$result = $conn->query("SELECT * FROM profile_details");

echo "<h2>All Matches</h2><div style='display: flex; flex-wrap: wrap;'>";

while ($row = $result->fetch_assoc()) {
    $imagePath = 'data:image/jpeg;base64,' . base64_encode($row['profile_image']);
    echo "<div style='margin: 10px; text-align: center; width: 150px;'>
        <img src='$imagePath' style='width: 100px; height: 100px; object-fit: cover; border-radius: 50%;'><br>
        {$row['profile_fname']}, {$row['age']}<br>
        {$row['caste']}, {$row['city']}
    </div>";
}
echo "</div>";
?>

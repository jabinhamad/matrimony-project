<?php
if (isset($_GET['query'])) {
  $search = htmlspecialchars($_GET['query']);

  // DB connection
  $conn = new mysqli("localhost", "root", "", "matrimony", 3307);
  if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
  }

  // Search by ID or name
  $sql = "SELECT * FROM profile_details WHERE id = '$search' OR profile_fname LIKE '%$search%' OR profile_lname LIKE '%$search%'";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    echo "<h2>Search Results:</h2><ul>";
    while ($row = $result->fetch_assoc()) {
      echo "<li>ID: " . $row['id'] . " - Name: " . $row['profile_fname'] . " " . $row['profile_lname'] . "</li>";
    }
    echo "</ul>";
  } else {
    echo "No matches found.";
  }

  $conn->close();
} else {
  echo "Please enter a search term.";
}
?>

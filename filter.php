<!DOCTYPE html>
<html>
<head>
    <title>Expectation Matches</title>
    <style>
        body { font-family: sans-serif; }
        form { width: 300px; margin: 0 auto; }
        input, button { width: 100%; margin: 5px 0; }
        .message { color: green; text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>

<h3 style="text-align:center;">Expectation Matches</h3>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $min_age = $_POST['min_age'];
    $max_age = $_POST['max_age'];
    $min_height = $_POST['min_height'];
    $max_height = $_POST['max_height'];
    $marital_status = $_POST['marital_status'];
    $religion = $_POST['religion'];
    $caste = $_POST['caste'];
    $education = $_POST['education'];
    $income = $_POST['income'];
    $place = $_POST['place'];
    $property = $_POST['property'];

    // Redirect to home.php with query parameters
    $query = http_build_query([
        'min_age' => $min_age,
        'max_age' => $max_age,
        'min_height' => $min_height,
        'max_height' => $max_height,
        'marital_status' => $marital_status,
        'religion' => $religion,
        'caste' => $caste,
        'education' => $education,
        'income' => $income,
        'place' => $place,
        'property' => $property
    ]);
    header("Location: hom.php?$query");
    exit();
}
?>

<form method="POST" action="">
    <input type="number" name="min_age" placeholder="Min Age" required>
    <input type="number" name="max_age" placeholder="Max Age" required>
    <input type="text" name="min_height" placeholder="Min Height" required>
    <input type="text" name="max_height" placeholder="Max Height" required>
    <input type="text" name="marital_status" placeholder="Marital Status">
    <input type="text" name="religion" placeholder="Religion">
    <input type="text" name="caste" placeholder="Caste">
    <input type="text" name="education" placeholder="Education">
    <input type="text" name="income" placeholder="Annual Income">
    <input type="text" name="place" placeholder="Place">
    <input type="text" name="property" placeholder="Property">
    <button type="submit">Find Matches</button>
</form>

</body>
</html>

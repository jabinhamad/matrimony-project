<?php
$conn = new mysqli("localhost", "root", "", "matrimony",3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Get the latest expectations (adjust user-specific logic as needed)
$exp_sql = "SELECT * FROM exp_matches ORDER BY id DESC LIMIT 1";
$exp_result = $conn->query($exp_sql);
$expectation = $exp_result->fetch_assoc();

// Step 2: Fetch All Matches
$all_matches = $conn->query("SELECT * FROM profile_details");

// Step 3: Fetch New Matches (latest 5 entries)
$new_matches = $conn->query("SELECT * FROM profile_details ORDER BY created_at DESC LIMIT 5");

// Step 4: Fetch Similar Matches - At least 6 fields match
$similar_sql = "
    SELECT pd.*, c.name AS city_name,
        (
            (c.name = '{$expectation['place']}') +
            (pd.religion = '{$expectation['religion']}') +
            (pd.caste = '{$expectation['caste']}') +
            (pd.education = '{$expectation['education']}') +
            (pd.income = '{$expectation['income']}') +
            (pd.property = '{$expectation['property']}') +
            (pd.marital_status = '{$expectation['marital_status']}') +
            (pd.age BETWEEN {$expectation['min_age']} AND {$expectation['max_age']}) +
            (pd.height >= '{$expectation['min_height']}' AND pd.height <= '{$expectation['max_height']}')
        ) AS match_score
    FROM profile_details pd
    LEFT JOIN city c ON pd.city_id = c.id
    HAVING match_score >= 6
";
$similar_matches = $conn->query($similar_sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            background-color: #f9f9f9;
        }
        h3 {
            margin-top: 30px;
            color: #444;
        }
        .section {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .profile {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            width: 130px;
            text-align: center;
            box-shadow: 1px 1px 5px rgba(0,0,0,0.1);
        }
        .profile img {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
        }
        .profile p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>

<h3>All Matches</h3>
<div class="section">
    <?php while($row = $all_matches->fetch_assoc()): ?>
        <div class="profile">
            <img src="<?= $row['photo_url'] ?? 'default.png' ?>" alt="profile">
            <p><?= $row['name'] ?><br><?= $row['age'] ?> yrs<br><?= $row['caste'] ?><br><?= $row['city'] ?></p>
        </div>
    <?php endwhile; ?>
</div>

<h3>New Matches</h3>
<div class="section">
    <?php while($row = $new_matches->fetch_assoc()): ?>
        <div class="profile">
            <img src="<?= $row['photo_url'] ?? 'default.png' ?>" alt="profile">
            <p><?= $row['name'] ?><br><?= $row['age'] ?> yrs<br><?= $row['caste'] ?><br><?= $row['city'] ?></p>
        </div>
    <?php endwhile; ?>
</div>

<h3>Similar Matches</h3>
<div class="section">
    <?php if ($similar_matches->num_rows > 0): ?>
        <?php while($row = $similar_matches->fetch_assoc()): ?>
            <div class="profile">
                <img src="<?= $row['photo_url'] ?? 'default.png' ?>" alt="profile">
                <p><?= $row['name'] ?><br><?= $row['age'] ?> yrs<br><?= $row['caste'] ?><br><?= $row['city'] ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No similar matches found.</p>
    <?php endif; ?>
</div>

</body>
</html>

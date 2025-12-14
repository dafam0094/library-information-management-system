<?php
session_start();
include '../db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all available labs
$result = $conn->query("SELECT * FROM laboratories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Laboratories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2 class="mb-4">Available Laboratories</h2>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Lab Name</th>
            <th>Location</th>
            <th>Capacity</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['lab_name']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= htmlspecialchars($row['capacity']) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<a href="lecturer_dashboard.php" class="btn btn-secondary">Back</a>
</body>
</html>

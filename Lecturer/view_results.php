<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit();
}

$results = $conn->query("
    SELECT e.experiment_id, u.full_name, e.experiment_title, e.results 
    FROM experiments e
    JOIN users u ON e.user_id = u.user_id
    WHERE e.results IS NOT NULL
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Experiment Results</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Student</th>
            <th>Experiment Title</th>
            <th>Result</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $results->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['experiment_title']) ?></td>
            <td><?= htmlspecialchars($row['results']) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<a href="lecturer_dashboard.php" class="btn btn-secondary">Back</a>
</body>
</html>

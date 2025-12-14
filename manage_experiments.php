<?php
session_start();
include 'db_connect.php';

// Restrict access to admin and lab users
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'lab_user'])) {
    $_SESSION['error'] = "Unauthorized access!";
    header("Location: login.php");
    exit();
}

// Fetch experiments with user and lab details
$sql = "SELECT e.experiment_id, e.experiment_title, e.description, e.experiment_date, e.results, 
               u.full_name, l.lab_name 
        FROM experiments e 
        JOIN users u ON e.user_id = u.user_id 
        JOIN laboratories l ON e.lab_id = l.lab_id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Experiments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Manage Experiments</h2>

        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <a href="add_experiment.php" class="btn btn-primary mb-3">Add New Experiment</a>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Lab</th>
                    <th>Conducted By</th>
                    <th>Results</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['experiment_id']; ?></td>
                        <td><?= htmlspecialchars($row['experiment_title']); ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td><?= htmlspecialchars($row['experiment_date']); ?></td>
                        <td><?= htmlspecialchars($row['lab_name']); ?></td>
                        <td><?= htmlspecialchars($row['full_name']); ?></td>
                        <td><?= htmlspecialchars($row['results'] ?: 'Pending'); ?></td>
                        <td>
                            <a href="edit_experiment.php?id=<?= $row['experiment_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_experiment.php?id=<?= $row['experiment_id']; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this experiment?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

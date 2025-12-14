<?php
session_start();
include '../db_connect.php'; // Ensure this connects to `olims_db`

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch assigned experiments for the student
$stmt = $conn->prepare("
    SELECT experiment_id, experiment_title, description, experiment_date, results 
    FROM experiments 
    WHERE user_id = ? 
    ORDER BY experiment_date ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Experiments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Student Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="student_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container mt-4">
    <h2 class="text-center">Assigned Experiments</h2>
    <p class="text-center text-muted">View the experiments assigned to you by your lecturer.</p>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?= htmlspecialchars($row['experiment_title']); ?></h5>
                            <p class="card-text"><strong>Description:</strong> <?= nl2br(htmlspecialchars($row['description'])); ?></p>
                            <p><strong>Date:</strong> <?= htmlspecialchars($row['experiment_date']); ?></p>
                            <p><strong>Results:</strong> <?= $row['results'] ? nl2br(htmlspecialchars($row['results'])) : "<span class='text-danger'>Not submitted</span>"; ?></p>
                            <a href="submit_experiment.php?experiment_id=<?= $row['experiment_id']; ?>" class="btn btn-success"><i class="fas fa-upload"></i> Submit Result</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-danger mt-4">No experiments assigned yet.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
include '../db_connect.php'; // Ensure this connects to `olims_db`

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if experiment_id is provided
if (!isset($_GET['experiment_id'])) {
    echo "<script>alert('Invalid request. No experiment selected.'); window.location.href = 'view_experiments.php';</script>";
    exit();
}

$experiment_id = intval($_GET['experiment_id']);
$experiment_title = $description = $experiment_date = $existing_result = "";

// Fetch experiment details
$stmt = $conn->prepare("SELECT experiment_title, description, experiment_date, results FROM experiments WHERE experiment_id = ? AND user_id = ?");
$stmt->bind_param("ii", $experiment_id, $user_id);
$stmt->execute();
$stmt->bind_result($experiment_title, $description, $experiment_date, $existing_result);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result_text = trim($_POST['experiment_result']);

    $update_stmt = $conn->prepare("UPDATE experiments SET results = ? WHERE experiment_id = ? AND user_id = ?");
    $update_stmt->bind_param("sii", $result_text, $experiment_id, $user_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Experiment result submitted successfully!'); window.location.href = 'view_experiments.php';</script>";
    } else {
        echo "<script>alert('Failed to submit result. Please try again.');</script>";
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Experiment Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 700px;
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
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="view_experiments.php">Experiments</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Experiment Submission Form -->
<div class="container mt-4">
    <h2 class="text-center">Submit Experiment Result</h2>
    <p class="text-center text-muted">Fill in your experiment findings below.</p>

    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title text-primary"><?= htmlspecialchars($experiment_title); ?></h5>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($description)); ?></p>
            <p><strong>Experiment Date:</strong> <?= htmlspecialchars($experiment_date); ?></p>

            <form method="post">
                <div class="mb-3">
                    <label for="experiment_result" class="form-label"><strong>Experiment Results:</strong></label>
                    <textarea id="experiment_result" name="experiment_result" class="form-control" rows="5" required><?= htmlspecialchars($existing_result); ?></textarea>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Submit Result</button>
                <a href="view_experiments.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
